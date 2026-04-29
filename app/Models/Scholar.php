<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Scholar extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'application_id',
        'student_id',
        'university',
        'course',
        'graduation_date',
    ];

    protected $casts = [
        'graduation_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function academicProgress(): HasMany
    {
        return $this->hasMany(AcademicProgress::class);
    }
}
