<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('study_summaries', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('book_id');
            $table->string('title');
            $table->longText('content')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants');
            $table->foreign('book_id')->references('id')->on('study_books')->cascadeOnDelete();
            $table->index('tenant_id');
            $table->index('book_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('study_summaries');
    }
};
