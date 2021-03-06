<?php

namespace Xguard\PhoneScheduler\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Row extends Model
{
    protected $table = 'ps_rows';

    protected $guarded = [];

    public function phoneLine(): BelongsTo
    {
        return $this->belongsTo(PhoneLine::class);
    }

    public function columns(): HasMany
    {
        return $this->HasMany(Column::class);
    }

}
