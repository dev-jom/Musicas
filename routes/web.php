<?php

use App\Http\Controllers\AlbumReviewController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AlbumReviewController::class, 'index'])->name('albums.index');
Route::get('/rascunhos', [AlbumReviewController::class, 'drafts'])->name('albums.drafts');
Route::post('/import', [AlbumReviewController::class, 'import'])->name('albums.import');
Route::get('/albums/{album}', [AlbumReviewController::class, 'show'])->name('albums.show');
Route::patch('/albums/{album}', [AlbumReviewController::class, 'updateAlbum'])->name('albums.update');
Route::delete('/albums/{album}', [AlbumReviewController::class, 'destroy'])->name('albums.destroy');
