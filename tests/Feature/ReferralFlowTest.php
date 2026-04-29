<?php

namespace Tests\Feature;

use App\Models\Referral;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ReferralFlowTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_referral_can_be_created_and_confirmed_with_reward_application(): void
    {
        $this->seed();

        $subscriberRoleId = Role::query()->where('name', Role::SUBSCRIBER)->value('id');

        $referrer = User::factory()->create([
            'role_id' => $subscriberRoleId,
            'email' => 'referrer@example.com',
        ]);

        $referee = User::factory()->create([
            'role_id' => $subscriberRoleId,
            'email' => 'referee@example.com',
        ]);

        $this->actingAs($referrer)
            ->post(route('referrals.store'), [
                'referee_id' => $referee->id,
            ])
            ->assertSessionHas('status');

        $referral = Referral::query()->where('referrer_id', $referrer->id)->where('referee_id', $referee->id)->firstOrFail();

        $this->actingAs($referee)
            ->patch(route('referrals.confirm', $referral))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('referrals', [
            'id' => $referral->id,
            'status' => 'confirmed',
            'reward_applied' => true,
        ]);

        $this->assertDatabaseHas('rewards', [
            'user_id' => $referrer->id,
            'type' => 'loyalty_points',
            'points' => 50,
        ]);
    }
}
