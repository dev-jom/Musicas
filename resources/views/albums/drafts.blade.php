<!DOCTYPE html>
<html lang="pt-BR" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rascunhos — Avaliacao de Musicas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root { --accent:#0dcaf0; --panel-bg:rgba(13,18,30,0.95); --panel-border:rgba(255,255,255,0.08); --sidebar-w:220px; }
        *{ box-sizing:border-box; }
        html,body{ height:100%; margin:0; }
        body{ background:#080c14; background-image:radial-gradient(ellipse at 60% 0%,#0d2137 0%,transparent 55%); font-size:.9rem; display:flex; }
        .sidebar{ width:var(--sidebar-w); flex-shrink:0; background:var(--panel-bg); border-right:1px solid var(--panel-border); display:flex; flex-direction:column; height:100vh; position:sticky; top:0; overflow-y:auto; }
        .sidebar-brand{ display:flex; align-items:center; gap:.6rem; padding:1.1rem 1rem .9rem; font-weight:700; font-size:.9rem; color:#fff; border-bottom:1px solid var(--panel-border); flex-shrink:0; }
        .sidebar-brand i{ color:var(--accent); font-size:1.15rem; }
        .sidebar-nav{ padding:.6rem .5rem; flex:1; }
        .sidebar-section-title{ font-size:.62rem; text-transform:uppercase; letter-spacing:.1em; color:rgba(255,255,255,.22); padding:.5rem .75rem .2rem; font-weight:600; }
        .sidebar-item{ display:flex; align-items:center; gap:.55rem; padding:.48rem .75rem; border-radius:.5rem; color:rgba(255,255,255,.5); text-decoration:none; font-size:.82rem; font-weight:500; transition:background .14s,color .14s; margin-bottom:.08rem; }
        .sidebar-item:hover{ background:rgba(255,255,255,.06); color:rgba(255,255,255,.9); }
        .sidebar-item.active{ background:rgba(13,202,240,.12); color:var(--accent); }
        .sidebar-item i{ font-size:.9rem; flex-shrink:0; }
        .sidebar-badge{ margin-left:auto; background:rgba(13,202,240,.15); color:var(--accent); font-size:.65rem; font-weight:700; border-radius:99px; padding:.08rem .4rem; }
        .sidebar-artist-item{ display:flex; align-items:center; gap:.5rem; padding:.4rem .75rem; border-radius:.5rem; color:rgba(255,255,255,.42); text-decoration:none; font-size:.78rem; transition:background .14s,color .14s; margin-bottom:.05rem; }
        .sidebar-artist-item:hover{ background:rgba(255,255,255,.05); color:rgba(255,255,255,.85); }
        .sidebar-footer{ padding:.75rem .9rem 1rem; border-top:1px solid var(--panel-border); flex-shrink:0; }
        .import-input{ background:rgba(0,0,0,.45); border-color:rgba(255,255,255,.12); color:#fff; font-size:.78rem; }
        .import-input::placeholder{ color:rgba(255,255,255,.28); }
        .import-input:focus{ border-color:var(--accent); box-shadow:0 0 0 2px rgba(13,202,240,.12); background:rgba(0,0,0,.6); }
        .main-wrap{ flex:1; min-width:0; display:flex; flex-direction:column; overflow-y:auto; height:100vh; }
        .main-content{ padding:1.5rem 1.6rem 3rem; flex:1; }
        .artist-section{ margin-bottom:2.2rem; }
        .artist-heading{ font-size:.78rem; font-weight:700; text-transform:uppercase; letter-spacing:.1em; color:rgba(255,255,255,.3); border-bottom:1px solid rgba(255,255,255,.07); padding-bottom:.4rem; margin-bottom:.9rem; display:flex; align-items:center; gap:.5rem; }
        .artist-heading span{ color:rgba(255,255,255,.55); }
        .album-card-link{ display:block; text-decoration:none; color:inherit; height:100%; }
        .album-card{ background:rgba(13,18,30,0.88); border:1px solid var(--panel-border); border-radius:.85rem; overflow:hidden; height:100%; display:flex; flex-direction:column; transition:border-color .2s,transform .18s,box-shadow .18s; }
        .album-card-link:hover .album-card{ border-color:rgba(255,193,7,0.4); transform:translateY(-3px); box-shadow:0 8px 26px rgba(255,193,7,.08); }
        .album-thumb-wrap{ aspect-ratio:1/1; overflow:hidden; background:#0a0e18; flex-shrink:0; position:relative; }
        .album-thumb-wrap img{ width:100%; height:100%; object-fit:cover; display:block; transition:transform .3s; }
        .album-card-link:hover .album-thumb-wrap img{ transform:scale(1.05); }
        .album-placeholder{ width:100%; height:100%; display:flex; align-items:center; justify-content:center; color:rgba(255,255,255,.07); font-size:2.8rem; }
        .draft-badge{ position:absolute; top:.45rem; right:.45rem; background:rgba(255,193,7,.18); border:1px solid rgba(255,193,7,.3); color:#ffc107; font-size:.6rem; font-weight:700; border-radius:.3rem; padding:.1rem .4rem; text-transform:uppercase; letter-spacing:.05em; backdrop-filter:blur(4px); }
        .album-info{ padding:.6rem .8rem .7rem; border-top:1px solid var(--panel-border); flex:1; display:flex; flex-direction:column; justify-content:space-between; min-height:64px; }
        .album-info .title{ font-weight:600; font-size:.82rem; color:#dce4f0; line-height:1.35; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
        .album-info .meta{ font-size:.68rem; color:rgba(255,255,255,.25); margin-top:.3rem; }
        .album-info .year-badge{ display:inline-block; font-size:.65rem; color:rgba(13,202,240,.6); font-weight:600; background:rgba(13,202,240,.07); border-radius:.3rem; padding:.05rem .35rem; margin-right:.35rem; }
    </style>
</head>
<body>

<aside class="sidebar">
    <div class="sidebar-brand">
        <i class="bi bi-music-note-beamed"></i>
        <span>Avaliacao de Musicas</span>
    </div>
    <nav class="sidebar-nav">
        <a href="{{ route('albums.index') }}" class="sidebar-item">
            <i class="bi bi-grid-fill"></i> Albums
        </a>
        <a href="{{ route('albums.drafts') }}" class="sidebar-item active">
            <i class="bi bi-pencil-square"></i> Rascunhos
            @if($byArtist->isNotEmpty())
                <span class="sidebar-badge">{{ $byArtist->flatten()->count() }}</span>
            @endif
        </a>
        @if($byArtist->isNotEmpty())
            <div class="sidebar-section-title mt-2">Artistas</div>
            @foreach($byArtist->keys()->sort() as $artistName)
                <a href="#artist-{{ Str::slug($artistName) }}" class="sidebar-artist-item">
                    <i class="bi bi-person-fill" style="font-size:.8rem;"></i>
                    {{ $artistName }}
                    <span class="sidebar-badge ms-auto">{{ $byArtist[$artistName]->count() }}</span>
                </a>
            @endforeach
        @endif
    </nav>
    <div class="sidebar-footer">
        <form method="POST" action="{{ route('albums.import') }}">
            @csrf
            <div class="mb-2">
                <input type="url" name="youtube_music_url" value="{{ old('youtube_music_url') }}" required
                    placeholder="URL do YouTube Music..."
                    class="import-input form-control form-control-sm">
            </div>
            <button type="submit" class="btn btn-info btn-sm w-100 fw-semibold">
                <i class="bi bi-cloud-download me-1"></i>Importar album
            </button>
        </form>
    </div>
</aside>

<div class="main-wrap">
    <div class="main-content">

        @if(session('status'))
            <div class="alert alert-success alert-dismissible py-2 mb-3" role="alert">
                <i class="bi bi-check-circle-fill me-1"></i>{{ session('status') }}
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible py-2 mb-3" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                @foreach($errors->all() as $err)<div>{{ $err }}</div>@endforeach
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div style="font-size:1.05rem;font-weight:700;color:#e8edf5;margin-bottom:1.2rem;">
            <i class="bi bi-pencil-square text-warning me-2 opacity-75"></i>Rascunhos
        </div>

        @if($byArtist->isEmpty())
            <div class="text-center py-5 text-secondary">
                <i class="bi bi-pencil-square display-4 d-block mb-3 opacity-25"></i>
                <p class="mb-1">Nenhum rascunho salvo.</p>
                <p class="small opacity-50">Importe um album e salve como rascunho.</p>
            </div>
        @else
            @foreach($byArtist->sortKeys() as $artistName => $albums)
                <div class="artist-section" id="artist-{{ Str::slug($artistName) }}">
                    <div class="artist-heading">
                        <i class="bi bi-person-fill opacity-50"></i>
                        <span>{{ $artistName }}</span>
                    </div>
                    <div class="row row-cols-2 row-cols-sm-3 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-3 align-items-stretch">
                        @foreach($albums->sortBy('release_year') as $album)
                            @php $coverSrc = $album->cover_path ? asset('storage/'.$album->cover_path) : ($album->cover_source_url ?? null) @endphp
                            <div class="col">
                                <a href="{{ route('albums.show', $album) }}" class="album-card-link">
                                    <div class="album-card">
                                        <div class="album-thumb-wrap">
                                            @if($coverSrc)
                                                <img src="{{ $coverSrc }}" alt="{{ $album->title }}">
                                            @else
                                                <div class="album-placeholder"><i class="bi bi-vinyl"></i></div>
                                            @endif
                                            <span class="draft-badge">Rascunho</span>
                                        </div>
                                        <div class="album-info">
                                            <div class="title">{{ $album->title }}</div>
                                            <div class="meta">
                                                @if($album->release_year)<span class="year-badge">{{ $album->release_year }}</span>@endif
                                                {{ $album->tracks_count }} {{ $album->tracks_count === 1 ? 'musica' : 'musicas' }}
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc4s9bIOgUxi8T/jzmT42FXCgpg+PcrMUlqkj5P4MHgl" crossorigin="anonymous"></script>
<script>
    document.querySelectorAll('[data-bs-dismiss="alert"]').forEach(function(btn){
        btn.addEventListener('click',function(){ var a=btn.closest('.alert'); if(a) a.remove(); });
    });
</script>
</body>
</html>
