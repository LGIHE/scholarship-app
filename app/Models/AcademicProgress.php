<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AcademicProgress extends Model
{
    use HasFactory;

    protected $table = 'academic_progress';

    protected $fillable = [
        'scholar_id',
        'academic_year',
        'semester',
        'gpa',
        'cgpa',
        'courses_taken',
        'achievements',
        'challenges',
        'notes',
    ];

    public function scholar(): BelongsTo
    {
        return $this->belongsTo(Scholar::class);
    }
}
