<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_offerings', function (Blueprint $table): void {
            $table->string('day_of_week', 20)->default('MWF')->after('room');
            $table->time('start_time')->default('09:00:00')->after('day_of_week');
            $table->time('end_time')->default('10:30:00')->after('start_time');
        });
    }

    public function down(): void
    {
        Schema::table('course_offerings', function (Blueprint $table): void {
            $table->dropColumn(['day_of_week', 'start_time', 'end_time']);
        });
    }
};
