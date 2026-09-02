<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class UserIdentity extends Model
{
    use HasFactory;
    use LogsActivity;

    public const PROVIDER_ORCID = 'orcid';
    public const PROVIDER_GITHUB = 'github';

    protected $fillable = [
        'user_id',
        'provider',
        'provider_user_id',
        'provider_username',
        'claims',
        'linked_at',
    ];

    protected $casts = [
        'claims' => 'array',
        'linked_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->fillable)
            ->logOnlyDirty()
            ->useLogName('user_identity')
            ->dontSubmitEmptyLogs();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
