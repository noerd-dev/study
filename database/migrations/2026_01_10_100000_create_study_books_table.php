<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('study_books', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->string('title');
            $table->string('author')->nullable();
            $table->integer('page_count')->nullable();
            $table->integer('publication_year')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants');
            $table->index('tenant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('study_books');
    }
};
