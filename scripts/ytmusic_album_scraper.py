#!/usr/bin/env python3
"""Extract album metadata and tracks from a YouTube Music album/playlist URL."""

import argparse
import json
import sys
from typing import Any

try:
    from yt_dlp import YoutubeDL
except ImportError:
    sys.stderr.write(
        "Dependencia ausente: yt-dlp. Instale com: pip install -r scripts/requirements.txt\n"
    )
    raise SystemExit(1)


def parse_args() -> argparse.Namespace:
    parser = argparse.ArgumentParser(description="Extract album data from YouTube Music")
    parser.add_argument("--url", required=True, help="YouTube Music album or playlist URL")
    return parser.parse_args()


def build_youtube_url(entry: dict[str, Any], playlist_id: str | None) -> str | None:
    webpage_url = entry.get("webpage_url")
    if isinstance(webpage_url, str) and webpage_url:
        return webpage_url

    video_id = entry.get("id")
    if not isinstance(video_id, str) or not video_id:
        return None

    if isinstance(playlist_id, str) and playlist_id:
        return f"https://music.youtube.com/watch?v={video_id}&list={playlist_id}"

    return f"https://music.youtube.com/watch?v={video_id}"


def _best_thumbnail(info: dict[str, Any]) -> str | None:
    """Return the best available thumbnail URL for the album/playlist.

    YouTube Music playlist-level thumbnails (i9.ytimg.com) are often 404.
    The first track's thumbnail (i.ytimg.com) is reliable and shows the album art.
    """
    entries = info.get("entries") or []

    # 1. First track thumbnail at maxresdefault (album art)
    for entry in entries:
        if isinstance(entry, dict):
            thumbs = entry.get("thumbnails")
            if isinstance(thumbs, list):
                # prefer maxresdefault webp > jpg entries
                for t in reversed(thumbs):
                    url = t.get("url") if isinstance(t, dict) else None
                    if isinstance(url, str) and "i.ytimg.com" in url:
                        # use the jpg maxresdefault for better compatibility
                        video_id = entry.get("id", "")
                        if video_id:
                            return f"https://i.ytimg.com/vi/{video_id}/maxresdefault.jpg"
                        return url
            t = entry.get("thumbnail")
            if isinstance(t, str) and t.startswith("http"):
                return t

    # 2. Playlist-level direct thumbnail field
    direct = info.get("thumbnail")
    if isinstance(direct, str) and direct.startswith("http"):
        return direct

    # 3. Playlist-level thumbnails list (may 404 but better than nothing)
    thumbs = info.get("thumbnails")
    if isinstance(thumbs, list) and thumbs:
        best = thumbs[-1]
        url = best.get("url") if isinstance(best, dict) else None
        if isinstance(url, str) and url.startswith("http"):
            return url

    return None


def main() -> int:
    args = parse_args()

    options = {
        "quiet": True,
        "skip_download": True,
        "extract_flat": False,
        "noplaylist": False,
    }

    try:
        with YoutubeDL(options) as ydl:
            info = ydl.extract_info(args.url, download=False)
    except Exception as exc:  # pylint: disable=broad-except
        sys.stderr.write(
            "Erro ao buscar album no YouTube Music. "
            "Verifique a URL e se o pacote yt-dlp esta instalado.\n"
        )
        sys.stderr.write(f"Detalhes: {exc}\n")
        return 1

    entries = info.get("entries") or []
    playlist_id = info.get("id")

    # Artist: prefer playlist-level fields, fall back to first track's uploader
    artist: str | None = None
    for field in ("artist", "uploader", "channel", "creator"):
        val = info.get(field)
        if isinstance(val, str) and val.strip():
            artist = val.strip()
            break
    if not artist:
        for entry in entries:
            if isinstance(entry, dict):
                for field in ("artist", "uploader", "channel", "creator"):
                    val = entry.get(field)
                    if isinstance(val, str) and val.strip():
                        artist = val.strip()
                        break
            if artist:
                break

    # Year: release_year field, or extract from upload_date (YYYYMMDD)
    release_year: int | None = info.get("release_year") or info.get("year")
    if not release_year:
        for entry in entries:
            if isinstance(entry, dict):
                ry = entry.get("release_year")
                if ry:
                    release_year = int(ry)
                    break
                ud = entry.get("upload_date")
                if isinstance(ud, str) and len(ud) >= 4 and ud[:4].isdigit():
                    release_year = int(ud[:4])
                    break

    tracks: list[dict[str, Any]] = []
    for index, entry in enumerate(entries, start=1):
        if not isinstance(entry, dict):
            continue

        title = entry.get("title")
        if not isinstance(title, str) or not title.strip():
            continue

        tracks.append(
            {
                "position": index,
                "title": title.strip(),
                "youtube_url": build_youtube_url(entry, playlist_id),
            }
        )

    payload = {
        "source_url":    args.url,
        "album_title":   info.get("title"),
        "artist":        artist,
        "release_year":  release_year,
        "cover_url":     _best_thumbnail(info),
        "tracks":        tracks,
    }

    json.dump(payload, sys.stdout, ensure_ascii=True)
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
