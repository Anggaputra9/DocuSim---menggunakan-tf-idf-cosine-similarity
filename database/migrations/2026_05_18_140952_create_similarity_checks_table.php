<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('similarity_checks', function (Blueprint $table) {
            $table->id();
            $table->string('input_title')->nullable();
            $table->longText('input_abstract');
            $table->float('highest_score')->default(0);
            $table->json('results'); // ranking hasil similarity
            $table->json('top_terms')->nullable(); // kata-kata penting (visualisasi)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('similarity_checks');
    }
};
