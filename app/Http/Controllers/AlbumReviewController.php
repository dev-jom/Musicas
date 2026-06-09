<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Track;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AlbumReviewController extends Controller
{
    public function index(Request $request): View
    {
        $allAlbums = Album::query()
            ->where('status', 'published')
            ->withCount('tracks')
            ->orderBy('release_year')
            ->latest()
            ->get();

        $byArtist = $allAlbums->groupBy(fn ($a) => $a->artist ?? 'Sem artista');
        $artists  = $byArtist->keys()->sort()->values();

        return view('albums.index', [
            'byArtist' => $byArtist,
            'artists'  => $artists,
        ]);
    }

    public function drafts(): View
    {
        $allDrafts = Album::query()
            ->where('status', 'draft')
            ->withCount('tracks')
            ->latest()
            ->get();

        $byArtist = $allDrafts->groupBy(fn ($a) => $a->artist ?? 'Sem artista');

        return view('albums.drafts', [
            'byArtist' => $byArtist,
        ]);
    }

    public function show(Album $album): View
    {
        $album->load('tracks');

        return view('albums.show', [
            'album' => $album,
        ]);
    }

    public function import(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'youtube_music_url' => ['required', 'url', 'regex:/music\.youtube\.com/'],
        ], [
            'youtube_music_url.regex' => 'Informe um link valido do YouTube Music.',
        ]);

        $scriptPath = base_path('scripts/ytmusic_album_scraper.py');

        if (! file_exists($scriptPath)) {
            return back()->withErrors([
                'youtube_music_url' => 'Script de importacao nao encontrado em scripts/ytmusic_album_scraper.py.',
            ]);
        }

        $pythonBinary = $this->resolvePythonBinary();

        $process = Process::path(base_path())->run([
            $pythonBinary,
            $scriptPath,
            '--url',
            $validated['youtube_music_url'],
        ]);

        if ($process->failed()) {
            $message = trim($process->errorOutput()) ?: trim($process->output());

            if (
                str_contains($message, "No module named 'yt_dlp'")
                || str_contains($message, 'No module named yt_dlp')
                || str_contains($message, 'Dependencia ausente: yt-dlp')
            ) {
                $message = 'Dependencia Python ausente: yt-dlp para o binario '.$pythonBinary.'. Rode: '.$pythonBinary.' -m pip install -r scripts/requirements.txt';
            }

            return back()->withErrors([
                'youtube_music_url' => $message !== ''
                    ? $message
                    : 'Nao foi possivel importar esse album agora.',
            ]);
        }

        $payload = json_decode($process->output(), true);

        if (! is_array($payload)) {
            return back()->withErrors([
                'youtube_music_url' => 'Resposta invalida do script Python.',
            ]);
        }

        $title        = (string) ($payload['album_title'] ?? 'Album sem titulo');
        $sourceUrl    = (string) ($payload['source_url'] ?? $validated['youtube_music_url']);
        $coverSourceUrl = (string) ($payload['cover_url'] ?? '');
        $artist       = isset($payload['artist']) && $payload['artist'] !== '' ? (string) $payload['artist'] : null;
        $releaseYear  = isset($payload['release_year']) && $payload['release_year'] ? (int) $payload['release_year'] : null;
        $tracks       = is_array($payload['tracks'] ?? null) ? $payload['tracks'] : [];

        if ($tracks === []) {
            return back()->withErrors([
                'youtube_music_url' => 'Nenhuma musica encontrada no link informado.',
            ]);
        }

        $album = DB::transaction(function () use ($title, $sourceUrl, $coverSourceUrl, $artist, $releaseYear, $tracks): Album {
            $album = Album::query()->firstOrNew([
                'source_url' => $sourceUrl,
            ]);

            $album->title        = $title;
            $album->artist       = $artist;
            $album->release_year = $releaseYear;
            $album->cover_source_url = $coverSourceUrl !== '' ? $coverSourceUrl : null;

            $coverPath = $this->downloadCover($coverSourceUrl);
            if ($coverPath !== null) {
                if ($album->cover_path !== null && Storage::disk('public')->exists($album->cover_path)) {
                    Storage::disk('public')->delete($album->cover_path);
                }

                $album->cover_path = $coverPath;
            }

            $album->save();

            $album->tracks()->delete();

            foreach ($tracks as $index => $trackData) {
                $trackTitle = trim((string) ($trackData['title'] ?? ''));
                if ($trackTitle === '') {
                    continue;
                }

                $album->tracks()->create([
                    'position' => (int) ($trackData['position'] ?? ($index + 1)),
                    'title' => $trackTitle,
                    'youtube_url' => $trackData['youtube_url'] ?? null,
                ]);
            }

            return $album;
        });

        return redirect()->route('albums.show', $album)->with('status', 'Album importado com sucesso.');
    }

    public function updateAlbum(Request $request, Album $album): RedirectResponse
    {
        $album->loadMissing('tracks');

        $albumTrackIds = $album->tracks->pluck('id')->toArray();

        $request->validate([
            'general_notes'  => ['nullable', 'string', 'max:10000'],
            'artist'         => ['nullable', 'string', 'max:255'],
            'release_year'   => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'tracks'         => ['nullable', 'array'],
            'tracks.*.rating'  => ['nullable', 'numeric', 'min:0', 'max:10'],
            'tracks.*.notes'   => ['nullable', 'string', 'max:5000'],
            'best_track_id'     => ['nullable', 'integer'],
            'favorite_track_id' => ['nullable', 'integer'],
            'least_track_id'    => ['nullable', 'integer'],
        ]);

        $bestId  = (int) $request->input('best_track_id', 0);
        $favId   = (int) $request->input('favorite_track_id', 0);
        $leastId = (int) $request->input('least_track_id', 0);

        // Only accept IDs that belong to this album
        $validSet = array_flip($albumTrackIds);
        if (! isset($validSet[$bestId]))  $bestId  = 0;
        if (! isset($validSet[$favId]))   $favId   = 0;
        if (! isset($validSet[$leastId])) $leastId = 0;

        $tracksInput = $request->input('tracks', []);

        $action = $request->input('action', 'draft');
        $newStatus = $action === 'publish' ? 'published' : 'draft';

        DB::transaction(function () use ($request, $album, $bestId, $favId, $leastId, $tracksInput, $newStatus): void {
            $album->update([
                'general_notes' => $request->input('general_notes'),
                'artist'        => $request->input('artist') ?: null,
                'release_year'  => $request->input('release_year') ? (int) $request->input('release_year') : null,
                'status'        => $newStatus,
            ]);

            foreach ($album->tracks as $track) {
                $data   = $tracksInput[$track->id] ?? [];
                $rating = (isset($data['rating']) && $data['rating'] !== '') ? (float) $data['rating'] : null;

                $track->update([
                    'rating'            => $rating,
                    'notes'             => $data['notes'] ?? null,
                    'is_best'           => $bestId  > 0 && $track->id === $bestId,
                    'is_favorite'       => $favId   > 0 && $track->id === $favId,
                    'is_least_favorite' => $leastId > 0 && $track->id === $leastId,
                    'is_downloaded'     => isset($data['is_downloaded']) && $data['is_downloaded'] == '1',
                ]);
            }
        });

        $message = $newStatus === 'published'
            ? '"'.$album->title.'" publicado com sucesso.'
            : '"'.$album->title.'" salvo como rascunho.';

        return redirect()->route('albums.show', $album)->with('status', $message);
    }

    public function destroy(Album $album): RedirectResponse
    {
        if ($album->cover_path !== null && Storage::disk('public')->exists($album->cover_path)) {
            Storage::disk('public')->delete($album->cover_path);
        }

        $album->delete();

        return redirect()->route('albums.index')->with('status', 'Album removido.');
    }

    public function updateAlbumNotes(Request $request, Album $album): RedirectResponse
    {
        $validated = $request->validate([
            'general_notes' => ['nullable', 'string', 'max:10000'],
        ]);

        $album->update([
            'general_notes' => $validated['general_notes'] ?? null,
        ]);

        return redirect()->route('albums.index')->with('status', 'Texto geral do album atualizado.');
    }

    public function updateTrack(Request $request, Track $track): RedirectResponse
    {
        $validated = $request->validate([
            'rating' => ['nullable', 'numeric', 'min:0', 'max:10'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'is_best' => ['nullable', 'boolean'],
            'is_favorite' => ['nullable', 'boolean'],
            'is_least_favorite' => ['nullable', 'boolean'],
        ]);

        $track->update([
            'rating' => $validated['rating'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'is_best' => $request->boolean('is_best'),
            'is_favorite' => $request->boolean('is_favorite'),
            'is_least_favorite' => $request->boolean('is_least_favorite'),
        ]);

        return redirect()->route('albums.index')->with('status', 'Musica atualizada.');
    }

    private function downloadCover(string $coverUrl): ?string
    {
        if ($coverUrl === '') {
            return null;
        }

        try {
            $response = Http::timeout(20)->get($coverUrl);

            if (! $response->successful()) {
                return null;
            }

            $extension = $this->guessImageExtension((string) $response->header('Content-Type'));
            $fileName = 'covers/'.Str::uuid()->toString().'.'.$extension;
            Storage::disk('public')->put($fileName, $response->body());

            return $fileName;
        } catch (\Throwable) {
            return null;
        }
    }

    private function guessImageExtension(string $contentType): string
    {
        return match (true) {
            str_contains($contentType, 'png') => 'png',
            str_contains($contentType, 'webp') => 'webp',
            str_contains($contentType, 'gif') => 'gif',
            default => 'jpg',
        };
    }

    private function resolvePythonBinary(): string
    {
        $configured = env('PYTHON_BIN');
        if (is_string($configured) && $configured !== '') {
            if (str_starts_with($configured, '/')) {
                if (file_exists($configured)) {
                    return $configured;
                }
            } else {
                $configuredPath = base_path($configured);
                if (file_exists($configuredPath)) {
                    return $configuredPath;
                }
            }
        }

        $venvPython = base_path('.venv/bin/python');

        if (file_exists($venvPython)) {
            return $venvPython;
        }

        $parentVenvPython = dirname(base_path()).'/.venv/bin/python';
        if (file_exists($parentVenvPython)) {
            return $parentVenvPython;
        }

        return 'python3';
    }
}
