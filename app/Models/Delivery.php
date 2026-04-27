<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Delivery extends Model
{
    use HasFactory;

    protected $table = 'deliveries';

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'box_id',
        'driver_id',
        'address_id',
        'status',
        'tracking_number',
        'estimated_delivery',
        'actual_delivery',
        'delivery_instructions',
        'stops_remaining',
        'eco_dispatch',
    ];

    protected $casts = [
        'estimated_delivery' => 'date',
        'actual_delivery' => 'datetime',
        'eco_dispatch' => 'boolean',
        'stops_remaining' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function box()
    {
        return $this->belongsTo(Box::class, 'box_id');
    }

    public function driver()
    {
        // Assuming Driver model exists or will exist in Mohy's scope
        return $this->belongsTo(Driver::class, 'driver_id');
    }

    public function address()
    {
        // Assuming Address model exists
        return $this->belongsTo(Address::class, 'address_id');
    }

    public function claims()
    {
        // Assuming Claim model exists
        return $this->hasMany(Claim::class, 'delivery_id');
    }
}
