<?php

use Livewire\Component;
use Noerd\Traits\NoerdList;
use Nywerk\Study\Models\StudyMaterial;

new class extends Component {
    use NoerdList;

    public $listModel = StudyMaterial::class;
    public ?string $detailRoute = 'study.study-material.detail';
    public $detailComponent = 'study::study-material-detail';
}; ?>

<x-noerd::page>
    <x-noerd::list />
</x-noerd::page>
