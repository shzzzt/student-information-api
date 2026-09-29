<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('course_code', 20)->unique();  // e.g. CS101
            $table->string('title', 200);
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('credits')->default(3);
            $table->unsignedSmallInteger('lecture_hours')->default(3);
            $table->unsignedSmallInteger('lab_hours')->default(0);
            $table->foreignId('program_id')
                  ->nullable()
                  ->constrained('programs')
                  ->nullOnDelete();
            $table->enum('status', [
                'active',
                'inactive',
                'archived',
            ])->default('active');
            $table->timestamps();

            // Frequently searched
            $table->index('course_code');
            $table->index('title');
            $table->index('status');
            $table->index('program_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};