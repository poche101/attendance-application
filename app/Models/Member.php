<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'first_name',
        'last_name',
        'email',
        'phone',
        'group',
        'church',
        'cell',
        'birthday',
        'is_active'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'birthday' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Get all of the attendance logs for the member.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}
