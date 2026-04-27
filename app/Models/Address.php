<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends BaseModel
{
    use HasFactory
    
    protected $keytype='string';

    public $incrementing=false;

    protected $fillable = [
    'user_id',
    'type', 
    'line_1',
    'line_2',
    'city',
    'postal_code',
    'country',
    'full_name', 
    'phone_number',
];

protected $casts = [
    
    'is_default' => 'boolean';

];

public function user()
{
    return $this->belongsTo(User::class);
}

public function subscriptions()
{
    return $this->hasMany(Subscription::class);
}

}