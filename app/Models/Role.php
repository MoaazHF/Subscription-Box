<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends BaseModel
{
    use HasFactory;

    public const SUBSCRIBER = 'subscriber';

    public const WAREHOUSE_STAFF = 'warehouse_staff';

    public const DRIVER = 'driver';

    public const ADMIN = 'admin';

    protected $keyType = 'int';

    public $incrementing = true;

    protected $fillable = ['name'];
}
