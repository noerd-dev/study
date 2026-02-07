<?php

use Illuminate\Support\Facades\Route;
use Nywerk\Study\Http\Controllers\FlashcardPrintController;

Route::group(['middleware' => ['web', 'auth', 'verified']], function (): void {
    Route::livewire('study/study-materials', 'study-materials-list')->name('study.study-materials');
    Route::livewire('study/study-material/{model}', 'study-material-detail')->name('study.study-material.detail');
    Route::livewire('study/summaries', 'summaries-list')->name('study.summaries');
    Route::livewire('study/summary/{model}', 'summary-detail')->name('study.summary.detail');
    Route::livewire('study/flashcards', 'flashcards-list')->name('study.flashcards');
    Route::livewire('study/flashcard/{model}', 'flashcard-detail')->name('study.flashcard.detail');
    Route::livewire('study/flashcards-print', 'flashcard-print-detail')->name('study.flashcards-print');
    Route::get('study/flashcards-print/pdf', [FlashcardPrintController::class, 'print'])->name('study.flashcards-print.pdf');
});
