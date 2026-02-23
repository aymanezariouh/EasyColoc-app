<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_admin' => 'boolean',
            'is_banned' => 'boolean',
            'reputation' => 'integer',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class);
    }

    public function colocations(): BelongsToMany
    {
        return $this->belongsToMany(Colocation::class, 'memberships')
            ->withPivot(['role', 'active', 'left_at'])
            ->withTimestamps();
    }

    public function activeMembership(): HasOne
    {
        return $this->hasOne(Membership::class)
            ->where('active', true)
            ->whereNull('left_at');
    }

    public function expensesPaid(): HasMany
    {
        return $this->hasMany(Expense::class, 'payer_id');
    }

    public function settlementsSent(): HasMany
    {
        return $this->hasMany(Settlement::class, 'from_user_id');
    }

    public function settlementsReceived(): HasMany
    {
        return $this->hasMany(Settlement::class, 'to_user_id');
    }
}
