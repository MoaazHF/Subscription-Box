<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AllergenTag extends Model
{
    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = ['name'];

    /** @return BelongsToMany<Item> */
    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'item_allergens', 'allergen_id', 'item_id');
    }

    /** @return BelongsToMany<User> */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_allergens', 'allergen_id', 'user_id');
    }
}
