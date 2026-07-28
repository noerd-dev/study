<?php

use Livewire\Attributes\Url;
use Livewire\Component;
use Noerd\Traits\NoerdDetail;
use Nywerk\Study\Models\StudyMaterial;

new class extends Component {
    use NoerdDetail;

    #[Url(as: 'studyMaterialId', keep: false, except: '')]
    public $modelId = null;

    public $detailModel = StudyMaterial::class;

    public function mount(): void
    {
        $this->initDetail();

        $this->setPreselect('study_material_id', $this->modelId);
    }

}; ?>

<x-noerd::page>
    <x-slot:header>
        <x-noerd::modal-title>{{ __('Study Material') }}</x-noerd::modal-title>
    </x-slot:header>

    <x-noerd::tab-content :layout="$pageLayout" :modelId="$modelId" />

    <x-slot:footer>
        <x-noerd::delete-save-bar :showDelete="isset($modelId)"/>
    </x-slot:footer>
</x-noerd::page>
