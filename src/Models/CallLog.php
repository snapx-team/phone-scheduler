<?php

namespace Xguard\PhoneScheduler\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CallLog extends Model
{
    protected $table = 'ps_call_logs';

    protected $guarded = [];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function phoneLine(): BelongsTo
    {
        return $this->belongsTo(PhoneLine::class);
    }
}
