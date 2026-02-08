<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Nywerk\Study\Models\Flashcard;
use Nywerk\Study\Models\StudyMaterial;
use Nywerk\Study\Tests\Traits\CreatesStudyUser;

uses(Tests\TestCase::class, RefreshDatabase::class);
uses(CreatesStudyUser::class);

beforeEach(function (): void {
    $this->user = $this->withStudyModule();
    $this->actingAs($this->user);
});

it('can set listFilters for study_material_id without error', function (): void {
    $studyMaterial = StudyMaterial::factory()->create(['tenant_id' => $this->user->selected_tenant_id]);

    Livewire::test('flashcards-list')
        ->set('listFilters.study_material_id', $studyMaterial->id)
        ->assertHasNoErrors();
});

it('applies study material filter to query results', function (): void {
    $studyMaterial1 = StudyMaterial::factory()->create(['tenant_id' => $this->user->selected_tenant_id]);
    $studyMaterial2 = StudyMaterial::factory()->create(['tenant_id' => $this->user->selected_tenant_id]);

    Flashcard::factory()->create([
        'study_material_id' => $studyMaterial1->id,
        'tenant_id' => $this->user->selected_tenant_id,
    ]);
    Flashcard::factory()->create([
        'study_material_id' => $studyMaterial2->id,
        'tenant_id' => $this->user->selected_tenant_id,
    ]);

    $component = Livewire::test('flashcards-list')
        ->set('listFilters.study_material_id', $studyMaterial1->id);

    $rows = $component->viewData('listConfig')['rows'];

    expect($rows)->toHaveCount(1);
    expect($rows->first()->study_material_id)->toBe($studyMaterial1->id);
});
