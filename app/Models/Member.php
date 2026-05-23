<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
    protected $fillable = [
        'title',
        'first_name',
        'last_name',
        'email',
        'phone',
        'church',
        'cell',
        'is_active',
    ];

    protected $casts = [
        'birthday'  => 'date',
        'is_active' => 'boolean',
    ];

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * "AO" initials from first + last name.
     */
    public function getInitialsAttribute(): string
    {
        return strtoupper(
            substr($this->first_name ?? '', 0, 1) .
            substr($this->last_name  ?? '', 0, 1)
        );
    }

    /**
     * Avatar background + text colour pair, cycled by ID.
     */
    public function getAvatarColorAttribute(): array
    {
        $bgs   = ['#dbeafe','#fce7f3','#d1fae5','#fef3c7','#ede9fe','#fee2e2','#e0f2fe','#fef9c3'];
        $texts = ['#1e40af','#1e40af','#1e40af','#1e40af','#5b21b6','#5b21b6','#0369a1','#5b21b6'];
        $i = $this->id % 8;
        return ['bg' => $bgs[$i], 'text' => $texts[$i]];
    }

    /**
     * Full name helper.
     */
    public function getFullNameAttribute(): string
    {
        return trim(($this->title ? $this->title . ' ' : '') . $this->first_name . ' ' . $this->last_name);
    }

    /**
     * Whether the member attended on a given date (defaults to today).
     */
    public function attendedOn(?string $date = null): bool
    {
        return $this->attendances()
            ->whereDate('attendance_date', $date ?? now()->toDateString())
            ->exists();
    }

    /**
     * Total number of times this member has attended.
     */
    public function getTotalAttendanceAttribute(): int
    {
        return $this->attendances()->count();
    }
}
