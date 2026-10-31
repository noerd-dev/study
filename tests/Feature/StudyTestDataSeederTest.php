<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Noerd\Helpers\TenantHelper;
use Noerd\Models\Tenant;
use Nywerk\Study\Database\Seeders\StudyTestDataSeeder;
use Nywerk\Study\Models\Flashcard;
use Nywerk\Study\Models\StudyMaterial;
use Nywerk\Study\Models\Summary;

uses(Tests\TestCase::class, RefreshDatabase::class);

beforeEach(function (): void {
    $this->tenant = Tenant::factory()->create();
    TenantHelper::setSelectedTenantId($this->tenant->id);

    $this->seed(StudyTestDataSeeder::class);
});

// The seeder always creates new rows (plain create, no firstOrCreate), so it is
// intentionally NOT idempotent — no re-seed test.

it('seeds materials with summaries and flashcards for the selected tenant', function (): void {
    expect(StudyMaterial::query()->ofTenant($this->tenant->id)->count())->toBe(6);
    expect(Summary::query()->acrossTenants()->where('tenant_id', '!=', $this->tenant->id)->count())->toBe(0);
    expect(Flashcard::query()->acrossTenants()->where('tenant_id', '!=', $this->tenant->id)->count())->toBe(0);

    StudyMaterial::query()->ofTenant($this->tenant->id)->get()->each(function (StudyMaterial $material): void {
        expect($material->summaries()->ofTenant($material->tenant_id)->count())->toBeGreaterThanOrEqual(3)
            ->toBeLessThanOrEqual(6);
        expect($material->flashcards()->ofTenant($material->tenant_id)->count())->toBeGreaterThanOrEqual(5)
            ->toBeLessThanOrEqual(10);
    });
});

it('links the seeded records to valid materials and summaries', function (): void {
    $materialIds = StudyMaterial::query()->ofTenant($this->tenant->id)->pluck('id');
    $summaryIds = Summary::query()->ofTenant($this->tenant->id)->pluck('id');

    // Roughly half of the flashcards are linked to a summary
    expect(Flashcard::query()->ofTenant($this->tenant->id)->whereNotNull('summary_id')->count())->toBeGreaterThan(0);
    expect(Flashcard::query()->ofTenant($this->tenant->id)->whereNull('summary_id')->count())->toBeGreaterThan(0);

    Summary::query()->ofTenant($this->tenant->id)->get()->each(function (Summary $summary) use ($materialIds): void {
        expect($materialIds)->toContain($summary->study_material_id);
    });

    Flashcard::query()->ofTenant($this->tenant->id)->get()->each(function (Flashcard $flashcard) use ($materialIds, $summaryIds): void {
        expect($materialIds)->toContain($flashcard->study_material_id);

        if ($flashcard->summary_id !== null) {
            expect($summaryIds)->toContain($flashcard->summary_id);
        }
    });
});
