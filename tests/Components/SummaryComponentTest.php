<?php


use Nywerk\Study\Models\StudyMaterial;
use Nywerk\Study\Models\Summary;
use Nywerk\Study\Tests\Traits\CreatesStudyUser;

uses(Tests\TestCase::class);
uses(CreatesStudyUser::class);

$testSettings = [
    'componentName' => 'summary-detail',
    'listName' => 'summaries-list',
    'id' => 'modelId',
    'urlParam' => 'summaryId',
];

it('test the route', function (): void {
    $user = $this->withStudyModule();

    $this->actingAs($user);

    $response = $this->get('/study/summaries');
    $response->assertStatus(200);
});

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
        ->set('detailData.title', $summaryTitle)
        ->set('detailData.study_material_id', $studyMaterial->id)
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

it('sets study_material_id from relationId on mount', function () use ($testSettings): void {
    $user = $this->withStudyModule();
    $studyMaterial = StudyMaterial::factory()->create(['tenant_id' => $user->selected_tenant_id]);

    $this->actingAs($user);

    Livewire::test($testSettings['componentName'], ['relationId' => $studyMaterial->id])
        ->assertSet('detailData.study_material_id', $studyMaterial->id)
        ->assertSet('relationTitles.study_material_id', $studyMaterial->title)
        ->assertHasNoErrors();
});
