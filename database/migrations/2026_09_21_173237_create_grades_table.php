<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')
                  ->unique()
                  ->constrained('enrollments')
                  ->cascadeOnDelete();
            $table->string('letter_grade', 5)->nullable();  // A, B+, etc.
            $table->decimal('numeric_grade', 5, 2)->nullable(); // 0.00 - 100.00
            $table->decimal('grade_points', 3, 2)->nullable();  // 0.00 - 4.00
            $table->enum('status', [
                'pending',
                'submitted',
                'approved',
                'disputed',
                'revised',
            ])->default('pending');
            $table->foreignId('graded_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->timestamp('graded_at')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('letter_grade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};