<?php

use Livewire\Component;
use Noerd\Traits\NoerdPage;
use Nywerk\Study\Models\Flashcard;
use Nywerk\Study\Models\StudyMaterial;
use Nywerk\Study\Models\Summary;

new class extends Component {
    use NoerdPage;

    public function with(): array
    {
        return [
            'studyMaterialsCount' => StudyMaterial::query()->count(),
            'summariesCount' => Summary::query()->count(),
            'flashcardsCount' => Flashcard::query()->count(),
        ];
    }
} ?>

<x-noerd::page>
    <div class="my-12">
        <div class="mb-12">
            <div class="font-semibold text-sm border-b border-gray-300 pb-2">
                {{ __('Overview') }}
            </div>
            <div class="flex">
                <x-noerd::dashboard-card heroicon="book-open" :title="__('Study Materials')"
                                        :value="$studyMaterialsCount"
                                        component="study::study-materials-list"/>
                <x-noerd::dashboard-card heroicon="document-text" :title="__('Summaries')"
                                        :value="$summariesCount"
                                        component="study::summaries-list"/>
                <x-noerd::dashboard-card heroicon="rectangle-stack" :title="__('Flashcards')"
                                        :value="$flashcardsCount"
                                        component="study::flashcards-list"/>
            </div>
        </div>

        <div class="mb-12">
            <div class="font-semibold text-sm border-b border-gray-300 pb-2">
                {{ __('Create') }}
            </div>
            <div class="flex">
                <x-noerd::dashboard-card heroicon="book-open" :title="__('New Study Material')"
                                        route="study.study-material.detail"
                                        component="study::study-material-detail"/>
                <x-noerd::dashboard-card heroicon="document-text" :title="__('New Summary')"
                                        route="study.summary.detail"
                                        component="study::summary-detail"/>
                <x-noerd::dashboard-card heroicon="rectangle-stack" :title="__('New Flashcard')"
                                        route="study.flashcard.detail"
                                        component="study::flashcard-detail"/>
            </div>
        </div>
    </div>
</x-noerd::page>
