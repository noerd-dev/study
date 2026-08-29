<?php


use Illuminate\Foundation\Testing\RefreshDatabase;
use Nywerk\Study\Models\StudyMaterial;
use Nywerk\Study\Models\Summary;
use Nywerk\Study\Tests\Traits\CreatesStudyUser;

uses(Tests\TestCase::class, RefreshDatabase::class);
uses(CreatesStudyUser::class);

$testSettings = [
    'componentName' => 'study::summary-detail',
    'listName' => 'study::summaries-list',
];

it('validates the data', function () use ($testSettings): void {
    $user = $this->withStudyModule();

    $this->actingAs($user);

    Livewire::test($testSettings['componentName'])
        ->call('store')
        ->assertHasErrors(['detailData.title'])
        ->assertHasErrors(['detailData.study_material_id']);
});

it('successfully stores the data', function () use ($testSettings): void {
    $user = $this->withStudyModule();

    $this->actingAs($user);
    $summaryTitle = fake()->sentence(3);

    $studyMaterial = StudyMaterial::factory()->create(['tenant_id' => $user->selected_tenant_id]);

    Livewire::test($testSettings['componentName'])
        ->set('detailData', validDetailPayload(Summary::class, [
            'tenant_id' => $user->selected_tenant_id,
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

it('it sets and removes the model id in url', function () use ($testSettings): void {
    $user = $this->withStudyModule();

    $this->actingAs($user);
    $studyMaterial = StudyMaterial::factory()->create(['tenant_id' => $user->selected_tenant_id]);
    $model = Summary::factory()->create(['tenant_id' => $user->selected_tenant_id, 'study_material_id' => $studyMaterial->id]);

    Livewire::test($testSettings['listName'])->call('listAction', $model->id)
        ->assertDispatched('noerdModal', modalComponent: $testSettings['componentName']);

    Livewire::withUrlParams(['summaryId' => $model->id])
        ->test($testSettings['componentName'])
        ->assertSet('modelId', $model->id)
        ->assertHasNoErrors();
});

it('mounts with study material relation title from existing summary', function () use ($testSettings): void {
    $user = $this->withStudyModule();
    $studyMaterial = StudyMaterial::factory()->create(['tenant_id' => $user->selected_tenant_id, 'title' => 'Clean Code']);

    $this->actingAs($user);

    $summary = Summary::factory()->create([
        'tenant_id' => $user->selected_tenant_id,
        'study_material_id' => $studyMaterial->id,
    ]);

    Livewire::withUrlParams(['summaryId' => $summary->id])
        ->test($testSettings['componentName'])
        ->assertSet('modelId', $summary->id)
        ->assertSet('detailData.study_material_id', $studyMaterial->id)
        ->assertSet('relationTitles.study_material_id', 'Clean Code')
        ->assertHasNoErrors();
});
