<aside class="sidebar">
    <div class="sidebar-brand">
        <i class="bi bi-music-note-beamed"></i>
        <span>Avaliacao de Musicas</span>
    </div>

    <nav class="sidebar-nav">
        <a href="{{ route('albums.index') }}" class="sidebar-item {{ request()->routeIs('albums.index') ? 'active' : '' }}">
            <i class="bi bi-grid-fill"></i> Albums
        </a>
        <a href="{{ route('albums.drafts') }}" class="sidebar-item {{ request()->routeIs('albums.drafts') ? 'active' : '' }}">
            <i class="bi bi-pencil-square"></i> Rascunhos
            @php $draftCount = \App\Models\Album::where('status','draft')->count(); @endphp
            @if ($draftCount > 0)
                <span class="sidebar-badge">{{ $draftCount }}</span>
            @endif
        </a>
    </nav>

    <div class="sidebar-footer">
        <form method="POST" action="{{ route('albums.import') }}">
            @csrf
            <div class="mb-2">
                <input
                    type="url"
                    name="youtube_music_url"
                    value="{{ old('youtube_music_url') }}"
                    required
                    placeholder="URL do YouTube Music..."
                    class="import-input form-control form-control-sm"
                >
            </div>
            <button type="submit" class="btn btn-info btn-sm w-100 fw-semibold">
                <i class="bi bi-cloud-download me-1"></i>Importar album
            </button>
        </form>
    </div>
</aside>
