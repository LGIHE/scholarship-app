<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'personal_info',
        'financial_info',
        'guardian_info',
        'essay',
        'scoring_breakdown',
        'status',
    ];

    protected $casts = [
        'personal_info' => 'array',
        'financial_info' => 'array',
        'guardian_info' => 'array',
        'essay' => 'array',
        'scoring_breakdown' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
