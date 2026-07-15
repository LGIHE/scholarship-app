<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Models\Cohort;

class Application extends Model
{
    use HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn (string $eventName) => match ($eventName) {
                'created' => 'Application submitted',
                'updated' => 'Application updated',
                'deleted' => 'Application deleted',
                default   => "Application {$eventName}",
            })
            ->useLogName('application');
    }

    protected $fillable = [
        'user_id',
        'cohort_id',
        'personal_info',
        'disability_info',
        'dependants_info',
        'financial_info',
        'guardian_info',
        'declaration_info',
        'essay',
        'documents',
        'scoring_breakdown',
        'status',
    ];

    protected $casts = [
        'personal_info'    => 'array',
        'disability_info'  => 'array',
        'dependants_info'  => 'array',
        'financial_info'   => 'array',
        'guardian_info'    => 'array',
        'declaration_info' => 'array',
        'essay'            => 'array',
        'documents'        => 'array',
        'scoring_breakdown' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cohort(): BelongsTo
    {
        return $this->belongsTo(Cohort::class);
    }
}
