<?php

namespace Xguard\PhoneScheduler\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Column extends Model
{
    protected $table = 'ps_columns';

    protected $guarded = [];

    public function row(): BelongsTo
    {
        return $this->belongsTo(Row::class);
    }

    public function employeeCards(): HasMany
    {
        return $this->hasMany(EmployeeCard::class)->orderBy('index', 'asc');
    }
}
