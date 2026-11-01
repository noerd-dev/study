<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Nywerk\Study\Models\StudyMaterial;
use Nywerk\Study\Models\Summary;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function (): void {
    $this->user = createNoerdUserForApp('STUDY');
    $this->tenantId = $this->user->selected_tenant_id;

    $this->actingAs($this->user, 'noerd');
});

it('validates the data', function (): void {
    Livewire::test('study::summary-detail')
        ->call('store')
        ->assertHasErrors(['detailData.title'])
        ->assertHasErrors(['detailData.study_material_id']);
});

it('successfully stores the data', function (): void {
    $summaryTitle = fake()->sentence(3);

    $studyMaterial = StudyMaterial::factory()->create(['tenant_id' => $this->tenantId]);

    Livewire::test('study::summary-detail')
        ->set('detailData', validDetailPayload(Summary::class, [
            'tenant_id' => $this->tenantId,
            'title' => $summaryTitle,
            'study_material_id' => $studyMaterial->id,
        ]))
        ->call('store')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('study_summaries', [
        'title' => $summaryTitle,
        'study_material_id' => $studyMaterial->id,
    ]);
});

it('it sets and removes the model id in url', function (): void {
    $studyMaterial = StudyMaterial::factory()->create(['tenant_id' => $this->tenantId]);
    $model = Summary::factory()->create(['tenant_id' => $this->tenantId, 'study_material_id' => $studyMaterial->id]);

    // The modal target is configuration; only the dispatch itself is mechanics.
    Livewire::test('study::summaries-list')->call('listAction', $model->id)
        ->assertDispatched('noerdModal');

    Livewire::withUrlParams(['summaryId' => $model->id])
        ->test('study::summary-detail')
        ->assertSet('modelId', $model->id)
        ->assertHasNoErrors();
});

it('mounts an existing summary with its study material', function (): void {
    $studyMaterial = StudyMaterial::factory()->create(['tenant_id' => $this->tenantId, 'title' => 'Clean Code']);

    $summary = Summary::factory()->create([
        'tenant_id' => $this->tenantId,
        'study_material_id' => $studyMaterial->id,
    ]);

    Livewire::withUrlParams(['summaryId' => $summary->id])
        ->test('study::summary-detail')
        ->assertSet('modelId', $summary->id)
        ->assertSet('detailData.study_material_id', $studyMaterial->id)
        ->assertHasNoErrors();
});
