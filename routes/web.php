<?php

use App\Http\Controllers\CorpusController;
use App\Http\Controllers\SimilarityController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SimilarityController::class, 'dashboard'])->name('dashboard');

// Similarity check
Route::prefix('similarity')->name('similarity.')->group(function () {
    Route::get('/check', [SimilarityController::class, 'create'])->name('create');
    Route::post('/check', [SimilarityController::class, 'check'])->name('check');
    Route::get('/history', [SimilarityController::class, 'history'])->name('history');
    Route::get('/{similarity}', [SimilarityController::class, 'show'])->name('show');
    Route::delete('/{similarity}', [SimilarityController::class, 'destroy'])->name('destroy');
});

// Corpus import / template — harus didefinisikan SEBELUM resource agar tidak ditangkap show
Route::get('corpus-template', [CorpusController::class, 'template'])->name('corpus.template');
Route::post('corpus-import', [CorpusController::class, 'import'])->name('corpus.import');

// Corpus CRUD
Route::resource('corpus', CorpusController::class)->parameters(['corpus' => 'corpus']);
