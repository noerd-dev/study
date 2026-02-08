<?php

declare(strict_types=1);

namespace Nywerk\Study\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Noerd\Models\Tenant;
use Nywerk\Study\Database\Seeders\StudyTestDataSeeder;
use Nywerk\Study\Models\Flashcard;
use Nywerk\Study\Models\StudyMaterial;
use Nywerk\Study\Models\Summary;

uses(\Tests\TestCase::class, RefreshDatabase::class);

beforeEach(function (): void {
    Tenant::factory()->create(['id' => 1]);
});

it('seeds study test data', function (): void {
    $this->seed(StudyTestDataSeeder::class);

    expect(StudyMaterial::count())->toBe(6);
});

it('creates summaries for each study material', function (): void {
    $this->seed(StudyTestDataSeeder::class);

    StudyMaterial::all()->each(function (StudyMaterial $material): void {
        $summaryCount = $material->summaries()->count();
        expect($summaryCount)->toBeGreaterThanOrEqual(3)
            ->toBeLessThanOrEqual(6);
    });
});

it('creates flashcards for each study material', function (): void {
    $this->seed(StudyTestDataSeeder::class);

    StudyMaterial::all()->each(function (StudyMaterial $material): void {
        $flashcardCount = $material->flashcards()->count();
        expect($flashcardCount)->toBeGreaterThanOrEqual(5)
            ->toBeLessThanOrEqual(10);
    });
});

it('links some flashcards to summaries', function (): void {
    $this->seed(StudyTestDataSeeder::class);

    $withSummary = Flashcard::whereNotNull('summary_id')->count();
    $withoutSummary = Flashcard::whereNull('summary_id')->count();

    expect($withSummary)->toBeGreaterThan(0);
    expect($withoutSummary)->toBeGreaterThan(0);
});

it('sets correct tenant_id on all records', function (): void {
    $this->seed(StudyTestDataSeeder::class);

    expect(StudyMaterial::where('tenant_id', 1)->count())->toBe(6);
    expect(Summary::where('tenant_id', '!=', 1)->count())->toBe(0);
    expect(Flashcard::where('tenant_id', '!=', 1)->count())->toBe(0);
});

it('creates valid foreign key relationships', function (): void {
    $this->seed(StudyTestDataSeeder::class);

    $materialIds = StudyMaterial::pluck('id');

    Summary::all()->each(function (Summary $summary) use ($materialIds): void {
        expect($materialIds)->toContain($summary->study_material_id);
    });

    $summaryIds = Summary::pluck('id');

    Flashcard::all()->each(function (Flashcard $flashcard) use ($materialIds, $summaryIds): void {
        expect($materialIds)->toContain($flashcard->study_material_id);

        if ($flashcard->summary_id !== null) {
            expect($summaryIds)->toContain($flashcard->summary_id);
        }
    });
});
