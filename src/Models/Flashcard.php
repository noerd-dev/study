<?php

namespace Nywerk\Study\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Noerd\Models\Tenant;
use Noerd\Traits\BelongsToTenant;
use Noerd\Traits\HasListScopes;
use Nywerk\Study\Database\Factories\FlashcardFactory;

class Flashcard extends Model
{
    use BelongsToTenant;
    use HasFactory;
    use HasListScopes;

    protected $table = 'study_flashcards';

    protected $fillable = [
        'tenant_id',
        'study_material_id',
        'summary_id',
        'question',
        'answer',
        'created_date',
    ];

    protected array $searchable = [
        'question',
        'answer',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function studyMaterial(): BelongsTo
    {
        return $this->belongsTo(StudyMaterial::class);
    }

    public function summary(): BelongsTo
    {
        return $this->belongsTo(Summary::class);
    }

    protected static function newFactory(): Factory
    {
        return FlashcardFactory::new();
    }

    protected function casts(): array
    {
        return [
            'created_date' => 'date',
        ];
    }
}
