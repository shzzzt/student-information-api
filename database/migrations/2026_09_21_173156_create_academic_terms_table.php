<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_terms', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();   // e.g. 2025-FALL
            $table->string('name', 100);            // Fall 2025
            $table->enum('term_type', [
                'fall',
                'spring',
                'summer',
                'winter',
                'intersession',
            ]);
            $table->unsignedSmallInteger('academic_year'); // e.g. 2025
            $table->date('start_date');
            $table->date('end_date');
            $table->date('registration_start')->nullable();
            $table->date('registration_end')->nullable();
            $table->enum('status', [
                'planned',
                'open',
                'closed',
                'archived',
            ])->default('planned');
            $table->timestamps();

            $table->index(['academic_year', 'term_type']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_terms');
    }
};