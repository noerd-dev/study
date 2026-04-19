<?php

use Illuminate\Support\Facades\Route;
use Nywerk\Study\Http\Controllers\FlashcardPrintController;

Route::group(['middleware' => ['web', 'auth', 'verified']], function (): void {
    Route::livewire('study', 'study::dashboard')->name('study.dashboard');
    Route::livewire('study/study-materials', 'study::study-materials-list')->name('study.study-materials');
    Route::livewire('study/study-material/{modelId}', 'study::study-material-detail')->name('study.study-material.detail');
    Route::livewire('study/summaries', 'study::summaries-list')->name('study.summaries');
    Route::livewire('study/summary/{modelId}', 'study::summary-detail')->name('study.summary.detail');
    Route::livewire('study/flashcards', 'study::flashcards-list')->name('study.flashcards');
    Route::livewire('study/flashcard/{modelId}', 'study::flashcard-detail')->name('study.flashcard.detail');
    Route::livewire('study/flashcards-print', 'study::flashcard-print-detail')->name('study.flashcards-print');
    Route::get('study/flashcards-print/pdf', [FlashcardPrintController::class, 'print'])->name('study.flashcards-print.pdf');
});
