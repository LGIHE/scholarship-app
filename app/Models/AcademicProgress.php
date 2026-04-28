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
        'semester',
        'year',
        'cgpa',
        'transcript_path',
    ];

    public function scholar(): BelongsTo
    {
        return $this->belongsTo(Scholar::class);
    }
}
