<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Nywerk\Study\Models\Flashcard;
use Nywerk\Study\Models\StudyMaterial;
use Nywerk\Study\Models\Summary;
use Nywerk\Study\Tests\Traits\CreatesStudyUser;

uses(Tests\TestCase::class, RefreshDatabase::class);
uses(CreatesStudyUser::class);

beforeEach(function (): void {
    $this->user = $this->withStudyModule();
    $this->actingAs($this->user);
});

it('loads study-material-detail via direct route', function (): void {
    $studyMaterial = StudyMaterial::factory()->create([
        'tenant_id' => $this->user->selected_tenant_id,
    ]);

    $this->get('/study/study-material/' . $studyMaterial->id)
        ->assertSuccessful()
        ->assertSeeLivewire('study-material-detail');
});

it('loads summary-detail via direct route', function (): void {
    $studyMaterial = StudyMaterial::factory()->create([
        'tenant_id' => $this->user->selected_tenant_id,
    ]);

    $summary = Summary::factory()->create([
        'tenant_id' => $this->user->selected_tenant_id,
        'study_material_id' => $studyMaterial->id,
    ]);

    $this->get('/study/summary/' . $summary->id)
        ->assertSuccessful()
        ->assertSeeLivewire('summary-detail');
});

it('loads flashcard-detail via direct route', function (): void {
    $studyMaterial = StudyMaterial::factory()->create([
        'tenant_id' => $this->user->selected_tenant_id,
    ]);

    $flashcard = Flashcard::factory()->create([
        'tenant_id' => $this->user->selected_tenant_id,
        'study_material_id' => $studyMaterial->id,
    ]);

    $this->get('/study/flashcard/' . $flashcard->id)
        ->assertSuccessful()
        ->assertSeeLivewire('flashcard-detail');
});
