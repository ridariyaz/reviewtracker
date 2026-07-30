<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A single customer rating event.
 * rating: good | ok | bad
 * status: new | in_progress | resolved (admin workflow)
 */
#[Fillable([
    'company_id',
    'employee_id',
    'rating',
    'comment',
    'status',
])]
class Feedback extends Model
{
    protected $table = 'feedback';

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
