<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicTerm extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'name', 'term_type', 'academic_year', 'start_date', 'end_date',
        'registration_start', 'registration_end', 'status',
    ];

    protected function casts(): array
    {
        return [
            'academic_year' => 'integer',
            'start_date' => 'date',
            'end_date' => 'date',
            'registration_start' => 'date',
            'registration_end' => 'date',
        ];
    }
}
