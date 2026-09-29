<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_offerings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')
                  ->constrained('courses')
                  ->cascadeOnDelete();
            $table->foreignId('academic_term_id')
                  ->constrained('academic_terms')
                  ->cascadeOnDelete();
            $table->foreignId('instructor_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->string('section', 10)->default('001');
            $table->string('room', 50)->nullable();
            $table->enum('delivery_mode', [
                'in_person',
                'online',
                'hybrid',
            ])->default('in_person');
            $table->unsignedSmallInteger('capacity')->default(30);
            $table->unsignedSmallInteger('enrolled_count')->default(0);
            $table->enum('status', [
                'scheduled',
                'open',
                'closed',
                'cancelled',
                'completed',
            ])->default('scheduled');
            $table->timestamps();

            // One offering per course/section/term
            $table->unique(
                ['course_id', 'academic_term_id', 'section'],
                'uq_offering_course_term_section'
            );

            $table->index('academic_term_id');
            $table->index('instructor_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_offerings');
    }
};