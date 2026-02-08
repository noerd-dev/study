<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;

uses(Tests\TestCase::class, RefreshDatabase::class);

it('has direct route for study-material-detail', function (): void {
    expect(Route::has('study.study-material.detail'))->toBeTrue();
});

it('has direct route for summary-detail', function (): void {
    expect(Route::has('study.summary.detail'))->toBeTrue();
});

it('has direct route for flashcard-detail', function (): void {
    expect(Route::has('study.flashcard.detail'))->toBeTrue();
});

it('has direct route for flashcards-print', function (): void {
    expect(Route::has('study.flashcards-print'))->toBeTrue();
});

it('has direct route for flashcards-print-pdf', function (): void {
    expect(Route::has('study.flashcards-print.pdf'))->toBeTrue();
});
