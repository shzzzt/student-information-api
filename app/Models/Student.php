<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'student_number', 'first_name', 'middle_name', 'last_name',
        'program_id', 'year_level', 'date_of_birth', 'gender', 'phone', 'address',
        'admission_date', 'expected_graduation_date', 'status', 'cumulative_gpa',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'program_id' => 'integer',
            'year_level' => 'integer',
            'date_of_birth' => 'date',
            'admission_date' => 'date',
            'expected_graduation_date' => 'date',
            'cumulative_gpa' => 'decimal:2',
        ];
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }
}
