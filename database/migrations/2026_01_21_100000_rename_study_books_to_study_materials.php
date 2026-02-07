<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        // Drop foreign keys first
        Schema::table('study_summaries', function (Blueprint $table): void {
            $table->dropForeign(['book_id']);
        });

        Schema::table('study_flashcards', function (Blueprint $table): void {
            $table->dropForeign(['book_id']);
        });

        // Rename the table
        Schema::rename('study_books', 'study_materials');

        // Rename the columns in related tables
        Schema::table('study_summaries', function (Blueprint $table): void {
            $table->renameColumn('book_id', 'study_material_id');
        });

        Schema::table('study_flashcards', function (Blueprint $table): void {
            $table->renameColumn('book_id', 'study_material_id');
        });

        // Re-add foreign keys with new references
        Schema::table('study_summaries', function (Blueprint $table): void {
            $table->foreign('study_material_id')->references('id')->on('study_materials')->cascadeOnDelete();
        });

        Schema::table('study_flashcards', function (Blueprint $table): void {
            $table->foreign('study_material_id')->references('id')->on('study_materials')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        // Drop new foreign keys
        Schema::table('study_summaries', function (Blueprint $table): void {
            $table->dropForeign(['study_material_id']);
        });

        Schema::table('study_flashcards', function (Blueprint $table): void {
            $table->dropForeign(['study_material_id']);
        });

        // Rename columns back
        Schema::table('study_summaries', function (Blueprint $table): void {
            $table->renameColumn('study_material_id', 'book_id');
        });

        Schema::table('study_flashcards', function (Blueprint $table): void {
            $table->renameColumn('study_material_id', 'book_id');
        });

        // Rename table back
        Schema::rename('study_materials', 'study_books');

        // Re-add original foreign keys
        Schema::table('study_summaries', function (Blueprint $table): void {
            $table->foreign('book_id')->references('id')->on('study_books')->cascadeOnDelete();
        });

        Schema::table('study_flashcards', function (Blueprint $table): void {
            $table->foreign('book_id')->references('id')->on('study_books')->cascadeOnDelete();
        });
    }
};
