<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class TeamTwoFlowTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_subscriber_gets_a_current_box_after_starting_a_subscription(): void
    {
        $this->seed();

        $user = User::query()->where('email', 'test@example.com')->firstOrFail();
        $plan = SubscriptionPlan::query()->where('name', 'standard')->firstOrFail();

        $address = Address::create([
            'user_id' => $user->id,
            'street' => '18 River Street',
            'city' => 'Cairo',
            'region' => 'Cairo',
            'country' => 'EG',
            'postal_code' => '11411',
            'is_default' => true,
        ]);

        $this->actingAs($user)->post(route('subscriptions.store'), [
            'plan_id' => $plan->id,
            'address_id' => $address->id,
            'start_date' => now()->toDateString(),
            'auto_renew' => 1,
            'eco_shipping' => 0,
        ])->assertRedirect(route('subscriptions.index'));

        $subscription = Subscription::query()
            ->whereBelongsTo($user)
            ->with('boxes.items')
            ->firstOrFail();

        $box = $subscription->boxes->first();

        $this->assertNotNull($box);
        $this->assertNotEmpty($box->theme);
        $this->assertGreaterThan(0, $box->items->count());

        $this->actingAs($user)
            ->get(route('boxes.index'))
            ->assertOk()
            ->assertSee((string) $box->period_year);

        $this->actingAs($user)
            ->get(route('boxes.show', $box))
            ->assertOk()
            ->assertSee($box->items->first()->name);
    }
}
