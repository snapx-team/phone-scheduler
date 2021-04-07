<?php

namespace Xguard\PhoneScheduler\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    protected $table = 'ps_employees';

    protected $guarded = [];

    public function employeeCards(): HasMany
    {
        return $this->hasMany(EmployeeCard::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }
}
