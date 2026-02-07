<?php

use Livewire\Component;
use Noerd\Scopes\SortScope;
use Noerd\Traits\NoerdList;
use Nywerk\Study\Models\Flashcard;

new class extends Component {
    use NoerdList;

    public ?int $studyMaterialId = null;

    public function listAction(mixed $modelId = null, array $relations = []): void
    {
        $this->dispatch(
            event: 'noerdModal',
            modalComponent: 'flashcard-detail',
            source: $this->getComponentName(),
            arguments: ['modelId' => $modelId, 'relations' => $this->studyMaterialId ? ['study_material_id' => $this->studyMaterialId] : $relations],
        );
    }

    public function with()
    {
        $rows = Flashcard::withoutGlobalScope(SortScope::class)
            ->with('studyMaterial', 'summary')
            ->when($this->studyMaterialId, function ($query): void {
                $query->where('study_material_id', $this->studyMaterialId);
            })
            ->orderBy($this->sortField ?: 'created_date', $this->sortAsc ? 'asc' : 'desc')
            ->paginate(self::PAGINATION);

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
        if ((int) request()->flashcardId) {
            $this->listAction(request()->flashcardId);
        }

        if (request()->create) {
            $this->listAction();
        }
    }
}; ?>

<x-noerd::page :disableModal="$disableModal">
    <x-noerd::list />
</x-noerd::page>
