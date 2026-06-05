<!DOCTYPE html>
<html lang="pt-BR" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Albums — Avaliacao de Musicas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --accent: #0dcaf0;
            --panel-bg: rgba(13,18,30,0.95);
            --panel-border: rgba(255,255,255,0.08);
            --sidebar-w: 230px;
        }
        * { box-sizing: border-box; }
        html, body { height: 100%; margin: 0; }
        body {
            background: #080c14;
            background-image: radial-gradient(ellipse at 60% 0%, #0d2137 0%, transparent 55%);
            font-size: 0.9rem;
            display: flex;
        }

        /* ---- Sidebar ---- */
        .sidebar {
            width: var(--sidebar-w);
            flex-shrink: 0;
            background: var(--panel-bg);
            border-right: 1px solid var(--panel-border);
            display: flex;
            flex-direction: column;
            height: 100vh;
            position: sticky;
            top: 0;
            overflow-y: auto;
        }
        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: .6rem;
            padding: 1.25rem 1.1rem 1rem;
            font-weight: 700;
            font-size: .92rem;
            color: #fff;
            border-bottom: 1px solid var(--panel-border);
            letter-spacing: .01em;
        }
        .sidebar-brand i { color: var(--accent); font-size: 1.2rem; }
        .sidebar-nav {
            padding: .75rem .6rem;
            flex: 1;
        }
        .sidebar-item {
            display: flex;
            align-items: center;
            gap: .6rem;
            padding: .55rem .75rem;
            border-radius: .55rem;
            color: rgba(255,255,255,.55);
            text-decoration: none;
            font-size: .84rem;
            font-weight: 500;
            transition: background .15s, color .15s;
            margin-bottom: .15rem;
            position: relative;
        }
        .sidebar-item:hover { background: rgba(255,255,255,.06); color: rgba(255,255,255,.9); }
        .sidebar-item.active { background: rgba(13,202,240,.12); color: var(--accent); }
        .sidebar-item i { font-size: .95rem; flex-shrink: 0; }
        .sidebar-badge {
            margin-left: auto;
            background: rgba(13,202,240,.2);
            color: var(--accent);
            font-size: .68rem;
            font-weight: 700;
            border-radius: 99px;
            padding: .1rem .45rem;
            line-height: 1.5;
        }
        .sidebar-footer {
            padding: .85rem 1rem 1.1rem;
            border-top: 1px solid var(--panel-border);
        }
        .import-input {
            background: rgba(0,0,0,.45);
            border-color: rgba(255,255,255,.12);
            color: #fff;
            font-size: .78rem;
        }
        .import-input::placeholder { color: rgba(255,255,255,.3); }
        .import-input:focus { border-color: var(--accent); box-shadow: 0 0 0 2px rgba(13,202,240,.12); background: rgba(0,0,0,.6); }

        /* ---- Main area ---- */
        .main-wrap {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            height: 100vh;
        }
        .main-content {
            padding: 1.75rem 1.75rem 3rem;
            flex: 1;
        }
        .page-heading {
            font-size: 1.1rem;
            font-weight: 700;
            color: #e8edf5;
            margin-bottom: 1.25rem;
        }

        /* ---- Album cards ---- */
        .album-card-link {
            display: block;
            text-decoration: none;
            color: inherit;
            height: 100%;
        }
        .album-card {
            background: rgba(13,18,30,0.9);
            border: 1px solid var(--panel-border);
            border-radius: .85rem;
            overflow: hidden;
            height: 100%;
            display: flex;
            flex-direction: column;
            transition: border-color .2s, transform .18s, box-shadow .18s;
        }
        .album-card-link:hover .album-card {
            border-color: rgba(13,202,240,0.45);
            transform: translateY(-3px);
            box-shadow: 0 8px 28px rgba(13,202,240,0.1);
        }
        .album-thumb-wrap {
            aspect-ratio: 1 / 1;
            overflow: hidden;
            background: #0a0e18;
            flex-shrink: 0;
        }
        .album-thumb-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform .3s;
        }
        .album-card-link:hover .album-thumb-wrap img { transform: scale(1.05); }
        .album-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: rgba(255,255,255,0.08);
            font-size: 3rem;
        }
        .album-info {
            padding: .65rem .85rem .75rem;
            border-top: 1px solid var(--panel-border);
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 68px;
        }
        .album-info .title {
            font-weight: 600;
            font-size: .84rem;
            color: #dce4f0;
            line-height: 1.35;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .album-info .meta {
            font-size: .7rem;
            color: rgba(255,255,255,.28);
            margin-top: .35rem;
            flex-shrink: 0;
        }
    </style>
</head>
<body>

@include('partials.sidebar')

<div class="main-wrap">
    <div class="main-content">

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

        <div class="page-heading">
            <i class="bi bi-grid-fill text-info me-2 opacity-75"></i>Albums
        </div>

        @if ($albums->isEmpty())
            <div class="text-center py-5 text-secondary">
                <i class="bi bi-vinyl display-4 d-block mb-3 opacity-25"></i>
                <p class="mb-1">Nenhum album publicado ainda.</p>
                <p class="small opacity-50">Importe um album pela barra lateral e clique em "Salvar tudo" para publicar.</p>
            </div>
        @else
            <div class="row row-cols-2 row-cols-sm-3 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-3 align-items-stretch">
                @foreach ($albums as $album)
                    @php
                        $coverSrc = $album->cover_path
                            ? asset('storage/'.$album->cover_path)
                            : ($album->cover_source_url ?? null);
                    @endphp
                    <div class="col">
                        <a href="{{ route('albums.show', $album) }}" class="album-card-link">
                            <div class="album-card">
                                <div class="album-thumb-wrap">
                                    @if ($coverSrc)
                                        <img src="{{ $coverSrc }}" alt="{{ $album->title }}">
                                    @else
                                        <div class="album-placeholder"><i class="bi bi-vinyl"></i></div>
                                    @endif
                                </div>
                                <div class="album-info">
                                    <div class="title">{{ $album->title }}</div>
                                    <div class="meta">{{ $album->tracks_count }} {{ $album->tracks_count === 1 ? 'musica' : 'musicas' }}</div>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
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
</script>
</body>
</html>
