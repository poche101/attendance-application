<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $fillable = [
        'member_id',
        'phone',
        'email',
        'attendance_date',
         'children_count',
        'submitted_at',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'submitted_at'    => 'datetime',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}
