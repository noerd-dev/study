<?php

use Livewire\Component;
use Noerd\Helpers\TenantHelper;
use Noerd\Traits\NoerdDetail;
use Nywerk\Study\Models\Flashcard;
use Nywerk\Study\Models\StudyMaterial;
use Nywerk\Study\Models\Summary;

new class () extends Component {
    use NoerdDetail;

    public function mount(): void
    {
        $this->initDetail();
        TenantHelper::setSelectedAppFromRoute();
    }

    public function with(): array
    {
        $selectedTenantId = TenantHelper::getSelectedTenantId();

        return [
            'studyMaterialsCount' => StudyMaterial::where('tenant_id', $selectedTenantId)->count(),
            'summariesCount' => Summary::where('tenant_id', $selectedTenantId)->count(),
            'flashcardsCount' => Flashcard::where('tenant_id', $selectedTenantId)->count(),
        ];
    }
} ?>

<x-noerd::page :disableModal="$disableModal">
    <div class="my-12">
        <div class="mb-12">
            <div class="font-semibold text-sm border-b border-gray-300 pb-2">
                {{ __('Overview') }}
            </div>
            <div class="flex">
                <x-noerd::dashboard-card heroicon="book-open" :title="__('Study Materials')"
                                        :value="$studyMaterialsCount"
                                        component="study-materials-list"/>
                <x-noerd::dashboard-card heroicon="document-text" :title="__('Summaries')"
                                        :value="$summariesCount"
                                        component="summaries-list"/>
                <x-noerd::dashboard-card heroicon="rectangle-stack" :title="__('Flashcards')"
                                        :value="$flashcardsCount"
                                        component="flashcards-list"/>
            </div>
        </div>

        <div class="mb-12">
            <div class="font-semibold text-sm border-b border-gray-300 pb-2">
                {{ __('Create') }}
            </div>
            <div class="flex">
                <x-noerd::dashboard-card heroicon="book-open" :title="__('New Study Material')"
                                        component="study-material-detail"/>
                <x-noerd::dashboard-card heroicon="document-text" :title="__('New Summary')"
                                        component="summary-detail"/>
                <x-noerd::dashboard-card heroicon="rectangle-stack" :title="__('New Flashcard')"
                                        component="flashcard-detail"/>
            </div>
        </div>
    </div>
</x-noerd::page>
