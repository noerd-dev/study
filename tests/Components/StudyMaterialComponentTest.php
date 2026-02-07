<?php

use Nywerk\Study\Models\StudyMaterial;
use Nywerk\Study\Tests\Traits\CreatesStudyUser;

uses(Tests\TestCase::class);
uses(CreatesStudyUser::class);

$testSettings = [
    'componentName' => 'study-material-detail',
    'listName' => 'study-materials-list',
    'id' => 'modelId',
    'urlParam' => 'studyMaterialId',
];

it('test the route', function (): void {
    $user = $this->withStudyModule();

    $this->actingAs($user);

    $response = $this->get('/study/study-materials');
    $response->assertStatus(200);
});

it('validates required fields via layout', function () use ($testSettings): void {
    $user = $this->withStudyModule();

    $this->actingAs($user);

    Livewire::test($testSettings['componentName'])
        ->call('store')
        ->assertHasErrors(['detailData.title']);
});

it('successfully stores the data', function () use ($testSettings): void {
    $user = $this->withStudyModule();

    $this->actingAs($user);
    $title = fake()->sentence(3);

    Livewire::test($testSettings['componentName'])
        ->set('detailData.title', $title)
        ->set('detailData.author', 'Test Author')
        ->call('store')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('study_materials', [
        'title' => $title,
        'author' => 'Test Author',
    ]);
});

it('sets and removes the model id in url', function () use ($testSettings): void {
    $user = $this->withStudyModule();

    $this->actingAs($user);
    $model = StudyMaterial::factory()->create(['tenant_id' => $user->selected_tenant_id]);

    Livewire::test($testSettings['listName'])->call('listAction', $model->id)
        ->assertDispatched('noerdModal', modalComponent: $testSettings['componentName']);

    Livewire::withUrlParams(['studyMaterialId' => $model->id])
        ->test($testSettings['componentName'])
        ->assertSet('modelId', $model->id)
        ->assertHasNoErrors();
});

it('loads existing study material data', function () use ($testSettings): void {
    $user = $this->withStudyModule();

    $this->actingAs($user);
    $studyMaterial = StudyMaterial::factory()->create([
        'tenant_id' => $user->selected_tenant_id,
        'title' => 'Test Book',
        'author' => 'Test Author',
    ]);

    Livewire::withUrlParams(['studyMaterialId' => $studyMaterial->id])
        ->test($testSettings['componentName'])
        ->assertSet('modelId', $studyMaterial->id)
        ->assertSet('detailData.title', 'Test Book')
        ->assertSet('detailData.author', 'Test Author')
        ->assertHasNoErrors();
});
