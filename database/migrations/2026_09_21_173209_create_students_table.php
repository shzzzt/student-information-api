<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
        $table->string('student_number', 30)->unique();
        $table->string('first_name', 100);   // ← ADD
        $table->string('middle_name', 100)->nullable(); // ← ADD
        $table->string('last_name', 100);    // ← ADD
        $table->foreignId('program_id')->constrained('programs')->restrictOnDelete();
        $table->date('date_of_birth')->nullable();
        $table->enum('gender', ['male', 'female', 'other', 'prefer_not_to_say'])->nullable();
        $table->string('phone', 30)->nullable();
        $table->text('address')->nullable();
        $table->date('admission_date');
        $table->date('expected_graduation_date')->nullable();
        $table->enum('status', ['active', 'inactive', 'on_leave', 'graduated', 'withdrawn', 'dismissed'])->default('active');
        $table->decimal('cumulative_gpa', 3, 2)->nullable();
        $table->timestamps();
        $table->softDeletes();

        $table->index('last_name');   // now valid
        $table->index('status');
        $table->index('program_id');
    });
}

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
