<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('study_flashcards', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('book_id');
            $table->text('question');
            $table->longText('answer')->nullable();
            $table->date('created_date')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants');
            $table->foreign('book_id')->references('id')->on('study_books')->cascadeOnDelete();
            $table->index('tenant_id');
            $table->index('book_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('study_flashcards');
    }
};
