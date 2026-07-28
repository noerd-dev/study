<?php

use Livewire\Component;
use Noerd\Traits\NoerdList;
use Nywerk\Study\Models\StudyMaterial;

new class extends Component {
    use NoerdList;

    public $listModel = StudyMaterial::class;
    public $detailComponent = 'study::study-material-detail';

    public function rendering()
    {
        if ((int) request()->studyMaterialId) {
            $this->listAction(request()->studyMaterialId);
        }

        if (request()->create) {
            $this->listAction();
        }
    }
}; ?>

<x-noerd::page>
    <x-noerd::list />
</x-noerd::page>
