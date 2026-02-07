<?php

declare(strict_types=1);


use Nywerk\Study\Models\Flashcard;
use Nywerk\Study\Models\StudyMaterial;
use Nywerk\Study\Tests\Traits\CreatesStudyUser;

uses(Tests\TestCase::class);
uses(CreatesStudyUser::class);

it('can view the flashcard print page', function (): void {
    $user = $this->withStudyModule();

    $this->actingAs($user);

    $response = $this->get('/study/flashcards-print');
    $response->assertStatus(200);
});

it('displays flashcards on the print page', function (): void {
    $user = $this->withStudyModule();
    $studyMaterial = StudyMaterial::factory()->create(['tenant_id' => $user->selected_tenant_id]);
    $flashcard = Flashcard::factory()->create([
        'tenant_id' => $user->selected_tenant_id,
        'study_material_id' => $studyMaterial->id,
        'question' => 'Test Question',
    ]);

    $this->actingAs($user);

    Livewire::test('flashcard-print-detail')
        ->assertSee('Test Question');
});

it('can select flashcards', function (): void {
    $user = $this->withStudyModule();
    $studyMaterial = StudyMaterial::factory()->create(['tenant_id' => $user->selected_tenant_id]);
    $flashcard = Flashcard::factory()->create([
        'tenant_id' => $user->selected_tenant_id,
        'study_material_id' => $studyMaterial->id,
    ]);

    $this->actingAs($user);

    Livewire::test('flashcard-print-detail')
        ->set('selectedFlashcards', [$flashcard->id])
        ->assertSet('selectedFlashcards', [$flashcard->id]);
});

it('validates at least one flashcard is selected', function (): void {
    $user = $this->withStudyModule();

    $this->actingAs($user);

    Livewire::test('flashcard-print-detail')
        ->call('generatePdf')
        ->assertHasErrors(['selection']);
});

it('validates maximum 8 flashcards', function (): void {
    $user = $this->withStudyModule();
    $studyMaterial = StudyMaterial::factory()->create(['tenant_id' => $user->selected_tenant_id]);
    $flashcards = Flashcard::factory()->count(9)->create([
        'tenant_id' => $user->selected_tenant_id,
        'study_material_id' => $studyMaterial->id,
    ]);

    $this->actingAs($user);

    Livewire::test('flashcard-print-detail')
        ->set('selectedFlashcards', $flashcards->pluck('id')->toArray())
        ->call('generatePdf')
        ->assertHasErrors(['selection']);
});

it('redirects to pdf route with valid selection', function (): void {
    $user = $this->withStudyModule();
    $studyMaterial = StudyMaterial::factory()->create(['tenant_id' => $user->selected_tenant_id]);
    $flashcard = Flashcard::factory()->create([
        'tenant_id' => $user->selected_tenant_id,
        'study_material_id' => $studyMaterial->id,
    ]);

    $this->actingAs($user);

    Livewire::test('flashcard-print-detail')
        ->set('selectedFlashcards', [$flashcard->id])
        ->call('generatePdf')
        ->assertHasNoErrors()
        ->assertRedirect();
});
