<?php

use Livewire\Component;
use Nywerk\Study\Models\Flashcard;

new class extends Component {
    public array $selectedFlashcards = [];

    public function generatePdf(): void
    {
        if (empty($this->selectedFlashcards)) {
            $this->addError('selection', __('study_label_no_flashcards_selected'));

            return;
        }

        if (count($this->selectedFlashcards) > 8) {
            $this->addError('selection', __('study_label_max_flashcards'));

            return;
        }

        $this->redirect(route('study.flashcards-print.pdf', ['flashcard_ids' => $this->selectedFlashcards]));
    }

    public function with(): array
    {
        $flashcards = Flashcard::with('studyMaterial')
            ->orderBy('created_date', 'desc')
            ->get();

        return [
            'flashcards' => $flashcards,
        ];
    }
}; ?>

<div class="p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">{{ __('study_label_print_flashcards') }}</h1>
        <p class="mt-2 text-sm text-gray-600">{{ __('study_label_select_flashcards') }}</p>
    </div>

    @error('selection')
        <div class="mb-4 rounded-md bg-red-50 p-4">
            <p class="text-sm text-red-700">{{ $message }}</p>
        </div>
    @enderror

    <div class="mb-6">
        <flux:button wire:click="generatePdf" variant="primary">
            {{ __('study_label_generate_pdf') }}
        </flux:button>
        <span class="ml-4 text-sm text-gray-500">
            {{ count($selectedFlashcards) }} / 8 {{ __('study_label_flashcards') }}
        </span>
    </div>

    <div class="space-y-3">
        @foreach($flashcards as $flashcard)
            @php
                $isSelected = in_array($flashcard->id, $selectedFlashcards);
            @endphp
            <label wire:key="flashcard-{{ $flashcard->id }}" class="flex items-start gap-4 rounded-lg border border-gray-200 p-4 cursor-pointer {{ $isSelected ? 'bg-blue-50 border-blue-300' : 'bg-white' }}">
                <div class="pt-1">
                    <input
                        type="checkbox"
                        wire:model.live="selectedFlashcards"
                        value="{{ $flashcard->id }}"
                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                    />
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-medium text-gray-900">{{ $flashcard->question }}</p>
                    @if($flashcard->studyMaterial)
                        <p class="mt-1 text-sm text-gray-500">{{ $flashcard->studyMaterial->title }}</p>
                    @endif
                </div>
            </label>
        @endforeach
    </div>

    @if($flashcards->isEmpty())
        <div class="text-center py-12">
            <p class="text-gray-500">{{ __('study_label_flashcards') }}: 0</p>
        </div>
    @endif
</div>
