<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Nywerk\Study\Models\StudyMaterial;
use Nywerk\Study\Models\Summary;
use Nywerk\Study\Tests\Traits\CreatesStudyUser;

uses(Tests\TestCase::class, RefreshDatabase::class);
uses(CreatesStudyUser::class);

$testSettings = [
    'componentName' => 'flashcard-detail',
    'listName' => 'flashcards-list',
    'id' => 'modelId',
    'urlParam' => 'flashcardId',
];

it('validates required fields', function () use ($testSettings): void {
    $user = $this->withStudyModule();

    $this->actingAs($user);

    Livewire::test($testSettings['componentName'])
        ->call('store')
        ->assertHasErrors(['detailData.question'])
        ->assertHasErrors(['detailData.study_material_id']);
});

it('successfully stores the data', function () use ($testSettings): void {
    $user = $this->withStudyModule();

    $this->actingAs($user);

    $studyMaterial = StudyMaterial::factory()->create(['tenant_id' => $user->selected_tenant_id]);

    Livewire::test($testSettings['componentName'])
        ->set('detailData.question', 'What is Laravel?')
        ->set('detailData.study_material_id', $studyMaterial->id)
        ->call('store')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('study_flashcards', [
        'question' => 'What is Laravel?',
        'study_material_id' => $studyMaterial->id,
    ]);
});

it('handles study material selection correctly', function () use ($testSettings): void {
    $user = $this->withStudyModule();
    $studyMaterial = StudyMaterial::factory()->create(['tenant_id' => $user->selected_tenant_id]);

    $this->actingAs($user);

    Livewire::test($testSettings['componentName'])
        ->call('studyMaterialSelected', $studyMaterial->id)
        ->assertSet('detailData.study_material_id', $studyMaterial->id)
        ->assertSet('relationTitles.study_material_id', $studyMaterial->title)
        ->assertHasNoErrors();
});

it('handles summary selection correctly', function () use ($testSettings): void {
    $user = $this->withStudyModule();
    $studyMaterial = StudyMaterial::factory()->create(['tenant_id' => $user->selected_tenant_id]);
    $summary = Summary::factory()->create([
        'tenant_id' => $user->selected_tenant_id,
        'study_material_id' => $studyMaterial->id,
    ]);

    $this->actingAs($user);

    Livewire::test($testSettings['componentName'])
        ->call('summarySelected', $summary->id)
        ->assertSet('detailData.summary_id', $summary->id)
        ->assertSet('relationTitles.summary_id', $summary->title)
        ->assertHasNoErrors();
});

it('preselects study material from session on mount', function () use ($testSettings): void {
    $user = $this->withStudyModule();
    $studyMaterial = StudyMaterial::factory()->create(['tenant_id' => $user->selected_tenant_id]);

    $this->actingAs($user);

    session(['listFilters' => ['study_material_id' => $studyMaterial->id]]);

    Livewire::test($testSettings['componentName'])
        ->assertSet('detailData.study_material_id', $studyMaterial->id)
        ->assertSet('relationTitles.study_material_id', $studyMaterial->title)
        ->assertHasNoErrors();
});

it('sets study_material_id from relations on mount', function () use ($testSettings): void {
    $user = $this->withStudyModule();
    $studyMaterial = StudyMaterial::factory()->create(['tenant_id' => $user->selected_tenant_id]);

    $this->actingAs($user);

    Livewire::test($testSettings['componentName'], ['relations' => ['study_material_id' => $studyMaterial->id]])
        ->assertSet('detailData.study_material_id', $studyMaterial->id)
        ->assertSet('relationTitles.study_material_id', $studyMaterial->title)
        ->assertHasNoErrors();
});
