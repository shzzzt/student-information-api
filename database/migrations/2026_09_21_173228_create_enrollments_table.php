<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')
                  ->constrained('students')
                  ->cascadeOnDelete();
            $table->foreignId('course_offering_id')
                  ->constrained('course_offerings')
                  ->cascadeOnDelete();
            $table->date('enrolled_on');
            $table->date('dropped_on')->nullable();
            $table->enum('status', [
                'enrolled',
                'waitlisted',
                'dropped',
                'withdrawn',
                'completed',
            ])->default('enrolled');
            $table->enum('grade_mode', [
                'graded',
                'pass_fail',
                'audit',
            ])->default('graded');
            $table->timestamps();

            // A student can only enroll once per offering
            $table->unique(
                ['student_id', 'course_offering_id'],
                'uq_student_offering'
            );

            $table->index('status');
            $table->index('enrolled_on');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};