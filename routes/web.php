<?php

use App\Http\Controllers\CandidateController;
use App\Http\Controllers\CandidatureController;
use App\Http\Controllers\PosteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicPosteController;
use Illuminate\Support\Facades\Route;

// Espace public : l'accueil liste les offres ouvertes et guide le visiteur dans sa candidature.
Route::get('/', [PublicPosteController::class, 'index'])->name('home');
Route::get('/offres/{poste}', [PublicPosteController::class, 'show'])->name('offres.show');
Route::get('/offres/{poste}/postuler', [CandidatureController::class, 'create'])->name('candidatures.create');
Route::post('/offres/{poste}/postuler', [CandidatureController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('candidatures.store');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::scopeBindings()->group(function () {
        Route::get('/postes/{poste}/candidatures', [CandidateController::class, 'index'])
            ->name('postes.candidatures.index');
        Route::patch('/postes/{poste}/candidatures/{candidate}', [CandidateController::class, 'update'])
            ->name('postes.candidatures.update');
    });

    Route::resource('postes', PosteController::class);
});

require __DIR__.'/auth.php';
