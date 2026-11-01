<?php

use Livewire\Component;
use Noerd\Facades\Noerd;
use Noerd\Traits\NoerdList;
use Nywerk\Study\Models\Flashcard;
use Nywerk\Study\Traits\StudyMaterialFilterTrait;

new class extends Component {
    use NoerdList;
    use StudyMaterialFilterTrait;

    public $listModel = Flashcard::class;
    public ?string $detailRoute = 'study.flashcard.detail';
    public $detailComponent = 'study::flashcard-detail';

    public ?int $studyMaterialId = null;

    protected array $listWith = ['studyMaterial', 'summary'];

    protected array $listScopes = ['study_material_id' => 'studyMaterialId'];

    public function listAction(mixed $modelId = null, array $relations = []): void
    {
        Noerd::modalFor('study.flashcard.detail', 'study::flashcard-detail', ['modelId' => $modelId, 'relations' => $this->studyMaterialId ? ['study_material_id' => $this->studyMaterialId] : $relations]);
    }

    public function listData(): array
    {
        $rows = $this->paginateList($this->listQuery($this->listModel));

        foreach ($rows as $row) {
            $row->studyMaterial = $row->studyMaterial?->title;
            $row->summary = $row->summary?->title;
        }

        return $this->buildList($rows);
    }
}; ?>

<x-noerd::page>
    <x-noerd::list :relations="$studyMaterialId ? ['study_material_id' => $studyMaterialId] : []" />
</x-noerd::page>
