<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    
    protected $keyType = 'string';
    public $incrementing = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'role_id',
        'email',
        'password',
        'full_name',
        'phone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Tell Laravel your password column is named 'password_hash'
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    //  A user belongs to one role
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    //  A user has many subscriptions
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    //  A user has many addresses
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }
}