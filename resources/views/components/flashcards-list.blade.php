<?php

use Livewire\Component;
use Noerd\Facades\Noerd;
use Noerd\Traits\NoerdList;
use Nywerk\Study\Models\Flashcard;
use Nywerk\Study\Traits\StudyMaterialFilterTrait;

new class extends Component {
    use NoerdList;
    use StudyMaterialFilterTrait;

    public ?int $studyMaterialId = null;

    public function listAction(mixed $modelId = null, array $relations = []): void
    {
        Noerd::modal('study::flashcard-detail', ['modelId' => $modelId, 'relations' => $this->studyMaterialId ? ['study_material_id' => $this->studyMaterialId] : $relations]);
    }

    public function with(): array
    {
        $rows = $this->listQuery(Flashcard::class)
            ->with('studyMaterial', 'summary')
            ->when($this->studyMaterialId, function ($query): void {
                $query->where('study_material_id', $this->studyMaterialId);
            })
            ->tap(fn ($query) => $this->applyListFilters($query))
            ->paginate($this->perPage);

        foreach ($rows as $row) {
            $row->studyMaterial = $row->studyMaterial?->title;
            $row->summary = $row->summary?->title;
        }

        return [
            'listConfig' => $this->buildList($rows),
        ];
    }

    public function rendering()
    {
        $this->loadListFilters();

        if ((int) request()->flashcardId) {
            $this->listAction(request()->flashcardId);
        }

        if (request()->create) {
            $this->listAction();
        }
    }
}; ?>

<x-noerd::page :disableModal="$disableModal">
    <x-noerd::list :relations="$studyMaterialId ? ['study_material_id' => $studyMaterialId] : []" />
</x-noerd::page>
