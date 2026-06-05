<!DOCTYPE html>
<html lang="pt-BR" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $album->title }} — Avaliacao de Musicas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --accent: #0dcaf0;
            --panel-bg: rgba(13,18,30,0.92);
            --panel-border: rgba(255,255,255,0.08);
            --row-hover: rgba(13,202,240,0.04);
        }
        body {
            background: #080c14;
            background-image: radial-gradient(ellipse at 20% 0%, #0d2137 0%, transparent 60%),
                              radial-gradient(ellipse at 80% 10%, #0d1a2e 0%, transparent 50%);
            min-height: 100vh;
            font-size: 0.9rem;
        }

        /* top bar */
        .top-bar {
            background: var(--panel-bg);
            border-bottom: 1px solid var(--panel-border);
            backdrop-filter: blur(12px);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .btn-back {
            background: rgba(255,255,255,.06);
            border: 1px solid rgba(255,255,255,.12);
            color: rgba(255,255,255,.8);
            border-radius: .5rem;
            padding: .3rem .75rem;
            font-size: .8rem;
            text-decoration: none;
            transition: all .15s;
        }
        .btn-back:hover { background: rgba(255,255,255,.12); color: #fff; }

        /* album card */
        .album-wrap {
            background: var(--panel-bg);
            border: 1px solid var(--panel-border);
            border-radius: 1rem;
            overflow: hidden;
        }
        .album-header {
            background: linear-gradient(135deg, rgba(13,202,240,0.07) 0%, transparent 60%);
            border-bottom: 1px solid var(--panel-border);
        }
        .album-cover {
            width: 130px;
            height: 130px;
            object-fit: cover;
            border-radius: .75rem;
            border: 2px solid rgba(13,202,240,0.2);
            flex-shrink: 0;
        }
        .album-cover-placeholder {
            width: 130px;
            height: 130px;
            border-radius: .75rem;
            border: 2px dashed rgba(255,255,255,0.12);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            color: rgba(255,255,255,0.15);
            font-size: 2.5rem;
        }

        /* track table */
        .track-table { border-collapse: separate; border-spacing: 0; }
        .track-table thead th {
            background: rgba(13,202,240,0.05);
            border-bottom: 1px solid rgba(13,202,240,0.18) !important;
            color: #0dcaf0;
            font-size: .68rem;
            text-transform: uppercase;
            letter-spacing: .09em;
            padding: .55rem 1rem;
        }
        .track-table tbody tr { border-bottom: 1px solid var(--panel-border); transition: background .15s; }
        .track-table tbody tr:last-child { border-bottom: none; }
        .track-table tbody tr:hover { background: var(--row-hover); }
        .track-table tbody td { padding: .6rem 1rem; vertical-align: middle; border: none; }

        /* rating */
        .rating-input {
            width: 68px;
            text-align: center;
            background: rgba(0,0,0,0.35);
            border: 1px solid rgba(255,255,255,0.12);
            color: #fff;
            border-radius: .5rem;
            padding: .3rem .4rem;
            font-weight: 700;
            font-size: .95rem;
        }
        .rating-input:focus { border-color: var(--accent); outline: none; box-shadow: 0 0 0 3px rgba(13,202,240,.15); }

        /* radio extras */
        .radio-pick {
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 2.1rem;
            height: 2.1rem;
            border-radius: .45rem;
            transition: background .12s;
        }
        .radio-pick:hover { background: rgba(255,255,255,.06); }
        .radio-pick input[type=radio] { position: absolute; opacity: 0; width: 0; height: 0; pointer-events: none; }
        .radio-pick .ri { font-size: 1.1rem; opacity: 0.18; transition: opacity .12s, transform .12s; }
        .radio-pick input[type=radio]:checked + .ri { opacity: 1; transform: scale(1.2); }
        .rb-best .ri  { color: #ffc107; }
        .rb-fav .ri   { color: #e85d7f; }
        .rb-least .ri { color: #8a929d; }

        /* comment */
        .notes-input {
            background: rgba(0,0,0,0.2);
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: .45rem;
            color: #9aa5b8;
            font-size: .78rem;
            resize: vertical;
            min-width: 160px;
            line-height: 1.4;
        }
        .notes-input:focus { border-color: rgba(13,202,240,0.4); color: #c8d4e8; background: rgba(0,0,0,0.35); outline: none; box-shadow: 0 0 0 2px rgba(13,202,240,.08); }
        .notes-input::placeholder { color: rgba(255,255,255,0.18); }

        /* general notes */
        .general-notes-area {
            background: rgba(0,0,0,.3);
            border: 1px solid rgba(255,255,255,.08);
            border-radius: .6rem;
            color: #c8d0de;
            font-size: .82rem;
            resize: vertical;
        }
        .general-notes-area:focus { border-color: var(--accent); outline: none; box-shadow: 0 0 0 3px rgba(13,202,240,.1); }

        /* buttons */
        .btn-save-all {
            background: rgba(13,202,240,.15);
            border: 1px solid rgba(13,202,240,.4);
            color: var(--accent);
            border-radius: .5rem;
            padding: .5rem 1.6rem;
            font-size: .88rem;
            font-weight: 600;
            transition: all .15s;
        }
        .btn-save-all:hover { background: var(--accent); color: #000; }

        .btn-draft {
            background: rgba(255,193,7,.08);
            border: 1px solid rgba(255,193,7,.3);
            color: #ffc107;
            border-radius: .5rem;
            padding: .5rem 1.2rem;
            font-size: .88rem;
            font-weight: 500;
            transition: all .15s;
        }
        .btn-draft:hover { background: rgba(255,193,7,.2); }

        .status-badge-published {
            display: inline-flex; align-items: center; gap: .3rem;
            background: rgba(13,202,240,.1); border: 1px solid rgba(13,202,240,.25);
            color: var(--accent); font-size: .7rem; font-weight: 600;
            border-radius: .4rem; padding: .15rem .55rem; text-transform: uppercase; letter-spacing: .05em;
        }
        .status-badge-draft {
            display: inline-flex; align-items: center; gap: .3rem;
            background: rgba(255,193,7,.1); border: 1px solid rgba(255,193,7,.25);
            color: #ffc107; font-size: .7rem; font-weight: 600;
            border-radius: .4rem; padding: .15rem .55rem; text-transform: uppercase; letter-spacing: .05em;
        }

        .btn-delete {
            background: rgba(220,53,69,.08);
            border: 1px solid rgba(220,53,69,.3);
            color: #dc3545;
            border-radius: .5rem;
            padding: .45rem 1.2rem;
            font-size: .85rem;
            font-weight: 500;
            transition: all .15s;
        }
        .btn-delete:hover { background: #dc3545; color: #fff; }

        .save-bar {
            background: rgba(0,0,0,.2);
            border-top: 1px solid var(--panel-border);
        }

        .open-link { font-size: .72rem; color: rgba(13,202,240,.65); text-decoration: none; }
        .open-link:hover { color: var(--accent); text-decoration: underline; }
        .track-num { color: rgba(255,255,255,0.28); font-size: .78rem; min-width: 1.4rem; display: inline-block; }

        /* download checkbox */
        .dl-wrap { cursor: pointer; display: inline-flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; border-radius: .4rem; transition: background .12s; }
        .dl-wrap:hover { background: rgba(255,255,255,.06); }
        .dl-wrap input[type=checkbox] { position: absolute; opacity: 0; width: 0; height: 0; pointer-events: none; }
        .dl-wrap .dl-icon { font-size: 1.1rem; color: rgba(255,255,255,.18); transition: color .12s, transform .12s; }
        .dl-wrap input[type=checkbox]:checked + .dl-icon { color: #20c997; transform: scale(1.15); }
    </style>
</head>
<body>

{{-- top bar --}}
<div class="top-bar py-2 px-3 px-md-4 mb-4">
    <div class="container-xl d-flex align-items-center gap-3">
        @if ($album->status === 'draft')
            <a href="{{ route('albums.drafts') }}" class="btn-back">
                <i class="bi bi-arrow-left me-1"></i>Rascunhos
            </a>
        @else
            <a href="{{ route('albums.index') }}" class="btn-back">
                <i class="bi bi-arrow-left me-1"></i>Albums
            </a>
        @endif
        <span class="text-white fw-semibold text-truncate" style="font-size:.92rem;">{{ $album->title }}</span>
        @if ($album->status === 'draft')
            <span class="status-badge-draft"><i class="bi bi-pencil-fill"></i> Rascunho</span>
        @else
            <span class="status-badge-published"><i class="bi bi-check2"></i> Publicado</span>
        @endif
    </div>
</div>

<div class="container-xl pb-5">

    @if (session('status'))
        <div class="alert alert-success alert-dismissible py-2 mb-3" role="alert">
            <i class="bi bi-check-circle-fill me-1"></i>{{ session('status') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible py-2 mb-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-1"></i>
            @foreach ($errors->all() as $err)<div>{{ $err }}</div>@endforeach
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    @endif

    <div class="album-wrap shadow-lg">

        {{-- form principal: envolve header + tabela + save bar --}}
        <form id="saveForm" method="POST" action="{{ route('albums.update', $album) }}">
            @csrf
            @method('PATCH')

            {{-- header --}}
            <div class="album-header p-3 p-md-4">
                <div class="d-flex gap-3 align-items-start flex-wrap">

                    @php
                        $coverSrc = $album->cover_path
                            ? asset('storage/'.$album->cover_path)
                            : ($album->cover_source_url ?? null);
                    @endphp

                    @if ($coverSrc)
                        <img src="{{ $coverSrc }}" alt="{{ $album->title }}" class="album-cover">
                    @else
                        <div class="album-cover-placeholder"><i class="bi bi-vinyl"></i></div>
                    @endif

                    <div class="flex-grow-1">
                        <h1 class="fw-bold text-white mb-0" style="font-size:1.25rem;">{{ $album->title }}</h1>
                        <a href="{{ $album->source_url }}" target="_blank" class="open-link mb-3 d-inline-block">
                            <i class="bi bi-box-arrow-up-right me-1"></i>Abrir no YouTube Music
                        </a>
                        <textarea
                            name="general_notes"
                            rows="3"
                            placeholder="Texto geral do album (opiniao, contexto, etc)..."
                            class="general-notes-area form-control form-control-sm w-100"
                        >{{ old('general_notes', $album->general_notes) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- track table --}}
            <div class="table-responsive">
                <table class="track-table w-100">
                    <thead>
                        <tr>
                            <th style="width:36%">Musica</th>
                            <th style="width:80px" class="text-center">Nota</th>
                            <th style="width:110px" class="text-center">
                                <span title="Melhor">🏆</span>
                                <span title="Favorita" class="mx-1">❤️</span>
                                <span title="Menos gostei">👎</span>
                            </th>
                            <th style="width:55px" class="text-center" title="Baixado">
                                <i class="bi bi-download" style="color:#20c997;"></i>
                            </th>
                            <th>Comentario</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($album->tracks as $track)
                            <tr>
                                <td>
                                    <span class="track-num">{{ $track->position }}.</span>
                                    <span class="fw-medium text-white">{{ $track->title }}</span>
                                    @if ($track->youtube_url)
                                        <br>
                                        <a href="{{ $track->youtube_url }}" target="_blank" class="open-link">
                                            <i class="bi bi-play-circle me-1"></i>ouvir
                                        </a>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <input
                                        type="number"
                                        name="tracks[{{ $track->id }}][rating]"
                                        value="{{ old('tracks.'.$track->id.'.rating', $track->rating !== null ? rtrim(rtrim((string)$track->rating,'0'),'.') : '') }}"
                                        min="0" max="10" step="0.5"
                                        placeholder="—"
                                        class="rating-input"
                                    >
                                </td>
                                <td class="text-center">
                                    <div class="d-flex gap-1 justify-content-center">
                                        <label class="radio-pick rb-best" title="Melhor musica">
                                            <input type="radio" name="best_track_id" value="{{ $track->id }}" @checked($track->is_best)>
                                            <i class="bi bi-trophy-fill ri"></i>
                                        </label>
                                        <label class="radio-pick rb-fav" title="Favorita">
                                            <input type="radio" name="favorite_track_id" value="{{ $track->id }}" @checked($track->is_favorite)>
                                            <i class="bi bi-heart-fill ri"></i>
                                        </label>
                                        <label class="radio-pick rb-least" title="Menos gostei">
                                            <input type="radio" name="least_track_id" value="{{ $track->id }}" @checked($track->is_least_favorite)>
                                            <i class="bi bi-hand-thumbs-down-fill ri"></i>
                                        </label>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <label class="dl-wrap" title="Baixado">
                                        <input type="checkbox" name="tracks[{{ $track->id }}][is_downloaded]" value="1" @checked($track->is_downloaded)>
                                        <i class="bi bi-download dl-icon"></i>
                                    </label>
                                </td>
                                <td>
                                    <textarea
                                        name="tracks[{{ $track->id }}][notes]"
                                        rows="2"
                                        placeholder="Comentario..."
                                        class="notes-input form-control form-control-sm w-100"
                                    >{{ old('tracks.'.$track->id.'.notes', $track->notes) }}</textarea>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- save bar --}}
            <div class="save-bar px-4 py-3 d-flex align-items-center gap-2 flex-wrap">
                <button type="submit" name="action" value="publish" class="btn-save-all">
                    <i class="bi bi-floppy-fill me-2"></i>Salvar tudo
                </button>
                <button type="submit" name="action" value="draft" class="btn-draft">
                    <i class="bi bi-pencil me-1"></i>Salvar como rascunho
                </button>
                <span class="text-secondary ms-1" style="font-size:.78rem;">
                    {{ $album->tracks->count() }} {{ $album->tracks->count() === 1 ? 'musica' : 'musicas' }}
                </span>
            </div>
        </form>

        {{-- delete zone (fora do form principal) --}}
        <div class="px-4 py-4" style="border-top: 1px solid rgba(220,53,69,0.1);">
            <form method="POST" action="{{ route('albums.destroy', $album) }}" onsubmit="return confirm('Tem certeza que deseja remover este album? Esta acao nao pode ser desfeita.')">
                @csrf
                @method('DELETE')
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <button type="submit" class="btn-delete">
                        <i class="bi bi-trash3 me-2"></i>Apagar album
                    </button>
                    <span class="text-secondary" style="font-size:.75rem;">Esta acao e permanente e nao pode ser desfeita.</span>
                </div>
            </form>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc4s9bIOgUxi8T/jzmT42FXCgpg+PcrMUlqkj5P4MHgl" crossorigin="anonymous"></script>
<script>
    document.querySelectorAll('[data-bs-dismiss="alert"]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var a = btn.closest('.alert');
            if (a) a.remove();
        });
    });

    // deselect radio ao clicar no ja marcado
    document.querySelectorAll('.radio-pick input[type=radio]').forEach(function (radio) {
        radio.addEventListener('mousedown', function () { this._wasChecked = this.checked; });
        radio.addEventListener('click', function () {
            if (this._wasChecked) this.checked = false;
            this._wasChecked = false;
        });
    });
</script>
</body>
</html>
