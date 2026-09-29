<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();      // e.g. BS-CS
            $table->string('name', 150);
            $table->string('department', 150)->nullable();
            $table->enum('degree_level', [
                'certificate',
                'associate',
                'bachelor',
                'master',
                'doctorate',
            ])->default('bachelor');
            $table->unsignedSmallInteger('total_credits')->nullable();
            $table->enum('status', ['active', 'inactive', 'archived'])
                  ->default('active');
            $table->timestamps();

            $table->index('degree_level');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};