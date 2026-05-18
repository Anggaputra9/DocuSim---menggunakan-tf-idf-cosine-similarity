<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('corpus_documents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('author')->nullable();
            $table->integer('year')->nullable();
            $table->string('category')->nullable();
            $table->longText('abstract');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('corpus_documents');
    }
};
