<?php

namespace Nywerk\Study\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Noerd\Models\Tenant;
use Noerd\Traits\BelongsToTenant;
use Nywerk\Study\Database\Factories\StudyMaterialFactory;

class StudyMaterial extends Model
{
    use BelongsToTenant;
    use HasFactory;

    protected $table = 'study_materials';

    protected $guarded = [];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function summaries(): HasMany
    {
        return $this->hasMany(Summary::class);
    }

    public function flashcards(): HasMany
    {
        return $this->hasMany(Flashcard::class);
    }

    protected static function newFactory(): Factory
    {
        return StudyMaterialFactory::new();
    }

    protected function casts(): array
    {
        return [
            'page_count' => 'integer',
            'publication_year' => 'integer',
        ];
    }
}
