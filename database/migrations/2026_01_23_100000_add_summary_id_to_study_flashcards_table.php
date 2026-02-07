<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('study_flashcards', function (Blueprint $table): void {
            $table->foreignId('summary_id')->nullable()->after('study_material_id')
                ->constrained('study_summaries')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('study_flashcards', function (Blueprint $table): void {
            $table->dropForeign(['summary_id']);
            $table->dropColumn('summary_id');
        });
    }
};
