<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Staff member under a company.
 *
 * - Public QR points at /review/{id}
 * - scans / good_count / ok_count / bad_count are denormalized counters
 * - Optional employee_username + employee_password for the employee portal (employee guard)
 */
#[Fillable([
    'company_id',
    'name',
    'scans',
    'good_count',
    'ok_count',
    'bad_count',
    'employee_username',
    'employee_password',
])]
#[Hidden(['employee_password'])]
class Employee extends Authenticatable
{
    protected $table = 'employees';

    /** Laravel Auth uses this for password verification on the employee guard. */
    public function getAuthPassword(): string
    {
        return (string) $this->employee_password;
    }

    protected function casts(): array
    {
        return [
            'employee_password' => 'hashed',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function feedback(): HasMany
    {
        return $this->hasMany(Feedback::class);
    }

    public function scanLogs(): HasMany
    {
        return $this->hasMany(ScanLog::class);
    }
}
