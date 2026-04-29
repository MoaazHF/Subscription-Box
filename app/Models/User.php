<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['role_id', 'name', 'phone', 'email', 'password', 'must_change_password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasUuids, Notifiable;

    public $incrementing = false;

    protected $keyType = 'string';

    protected function casts(): array
    {
        return [
            'role_id' => 'integer',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'must_change_password' => 'boolean',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function driver(): HasOne
    {
        return $this->hasOne(Driver::class);
    }

    public function warehouseStaffProfile(): HasOne
    {
        return $this->hasOne(WarehouseStaff::class);
    }

    public function allergens(): BelongsToMany
    {
        return $this->belongsToMany(AllergenTag::class, 'user_allergens', 'user_id', 'allergen_tag_id');
    }

    public function rewards(): HasMany
    {
        return $this->hasMany(Reward::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function createdPromoCodes(): HasMany
    {
        return $this->hasMany(PromoCode::class, 'created_by');
    }

    public function purchasedGifts(): HasMany
    {
        return $this->hasMany(GiftSubscription::class, 'purchaser_id');
    }

    public function receivedGifts(): HasMany
    {
        return $this->hasMany(GiftSubscription::class, 'recipient_user_id');
    }

    public function socialPosts(): HasMany
    {
        return $this->hasMany(SocialPost::class);
    }

    public function referralsMade(): HasMany
    {
        return $this->hasMany(Referral::class, 'referrer_id');
    }

    public function referralsReceived(): HasMany
    {
        return $this->hasMany(Referral::class, 'referee_id');
    }

    public function isAdmin(): bool
    {
        return $this->role?->name === Role::ADMIN;
    }

    public function isDriver(): bool
    {
        return $this->role?->name === Role::DRIVER;
    }

    public function isWarehouseStaff(): bool
    {
        return $this->role?->name === Role::WAREHOUSE_STAFF;
    }

    public function isSubscriber(): bool
    {
        return $this->role?->name === Role::SUBSCRIBER;
    }
}
