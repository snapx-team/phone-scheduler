<?php

namespace Xguard\PhoneScheduler\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PhoneLine extends Model
{
    protected $table = 'ps_phone_lines';

    protected $guarded = [];

    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    public function rows(): HasMany
    {
        return $this->hasMany(Row::class);
    }
}
