<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Noerd\Models\NoerdUser;
use Noerd\Models\Tenant;
use Nywerk\Study\Models\Flashcard;
use Nywerk\Study\Models\StudyMaterial;
use Nywerk\Study\Tests\Traits\CreatesStudyUser;

uses(Tests\TestCase::class, RefreshDatabase::class);
uses(CreatesStudyUser::class);

beforeEach(function (): void {
    $this->user = $this->withStudyModule();
    $this->tenantId = $this->user->selected_tenant_id;

    $this->actingAs($this->user);
});

it('displays flashcards on the print page', function (): void {
    $studyMaterial = StudyMaterial::factory()->create(['tenant_id' => $this->tenantId]);
    Flashcard::factory()->create([
        'tenant_id' => $this->tenantId,
        'study_material_id' => $studyMaterial->id,
        'question' => 'Test Question',
    ]);

    Livewire::test('study::flashcard-print-page')
        ->assertSee('Test Question');
});

it('validates at least one flashcard is selected', function (): void {
    Livewire::test('study::flashcard-print-page')
        ->call('generatePdf')
        ->assertHasErrors(['selection']);
});

it('validates maximum 8 flashcards', function (): void {
    $studyMaterial = StudyMaterial::factory()->create(['tenant_id' => $this->tenantId]);
    $flashcards = Flashcard::factory()->count(9)->create([
        'tenant_id' => $this->tenantId,
        'study_material_id' => $studyMaterial->id,
    ]);

    Livewire::test('study::flashcard-print-page')
        ->set('selectedFlashcards', $flashcards->pluck('id')->toArray())
        ->call('generatePdf')
        ->assertHasErrors(['selection']);
});

it('redirects to pdf route with valid selection', function (): void {
    $studyMaterial = StudyMaterial::factory()->create(['tenant_id' => $this->tenantId]);
    $flashcard = Flashcard::factory()->create([
        'tenant_id' => $this->tenantId,
        'study_material_id' => $studyMaterial->id,
    ]);

    Livewire::test('study::flashcard-print-page')
        ->set('selectedFlashcards', [$flashcard->id])
        ->call('generatePdf')
        ->assertHasNoErrors()
        ->assertRedirect();
});

it('renders the selected flashcards as a pdf', function (): void {
    $studyMaterial = StudyMaterial::factory()->create(['tenant_id' => $this->tenantId]);
    $flashcard = Flashcard::factory()->create([
        'tenant_id' => $this->tenantId,
        'study_material_id' => $studyMaterial->id,
    ]);

    $this->get(route('study.flashcards-print.pdf', ['flashcard_ids' => [$flashcard->id]]))
        ->assertOk()
        ->assertHeader('Content-Type', 'application/pdf');
});

it('refuses to print a flashcard belonging to another tenant', function (): void {
    $otherTenant = Tenant::factory()->create();
    $otherUser = NoerdUser::factory()->create();
    $otherUser->tenants()->attach($otherTenant->id);

    $foreignMaterial = StudyMaterial::factory()->create(['tenant_id' => $otherTenant->id]);
    $foreignFlashcard = Flashcard::factory()->create([
        'tenant_id' => $otherTenant->id,
        'study_material_id' => $foreignMaterial->id,
    ]);

    // Nothing is left to print once the tenant scope has dropped the row, so the
    // controller must bounce back instead of rendering someone else's card.
    $this->from(route('study.flashcards-print'))
        ->get(route('study.flashcards-print.pdf', ['flashcard_ids' => [$foreignFlashcard->id]]))
        ->assertRedirect(route('study.flashcards-print'))
        ->assertSessionHasErrors('selection');
});
