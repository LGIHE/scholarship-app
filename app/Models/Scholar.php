<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Scholar extends Model
{
    use HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['university', 'course', 'student_id', 'graduation_date'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn (string $eventName) => match ($eventName) {
                'created' => 'Scholar record created',
                'updated' => 'Scholar record updated',
                'deleted' => 'Scholar record deleted',
                default   => "Scholar {$eventName}",
            })
            ->useLogName('scholar');
    }

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
