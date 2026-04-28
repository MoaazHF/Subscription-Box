<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Claim;
use App\Models\Delivery;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class ClaimTest extends TestCase
{
    use LazilyRefreshDatabase;

    private function makeDeliveryForUser(User $user): Delivery
    {
        $address = Address::create([
            'id' => (string) Str::uuid(),
            'user_id' => $user->id,
            'street' => '1 Test Street',
            'city' => 'Alexandria',
            'country' => 'Egypt',
        ]);

        return Delivery::create([
            'id' => (string) Str::uuid(),
            'box_id' => (string) Str::uuid(),
            'address_id' => $address->id,
            'status' => 'delivered',
        ]);
    }

    public function test_shows_the_create_claim_form(): void
    {
        $user = User::factory()->create();
        $delivery = $this->makeDeliveryForUser($user);

        $this->actingAs($user)
            ->get(route('claims.create', $delivery))
            ->assertOk()
            ->assertSee('File a Claim');
    }

    public function test_stores_a_claim_without_a_photo(): void
    {
        $user = User::factory()->create();
        $delivery = $this->makeDeliveryForUser($user);

        $this->actingAs($user)
            ->post(route('claims.store', $delivery), [
                'type' => 'damaged',
                'description' => 'Several items in the box were cracked on arrival.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('claims', [
            'delivery_id' => $delivery->id,
            'type' => 'damaged',
            'status' => 'open',
        ]);
    }

    public function test_stores_a_claim_with_a_photo(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $delivery = $this->makeDeliveryForUser($user);

        $this->actingAs($user)
            ->post(route('claims.store', $delivery), [
                'type' => 'missing',
                'description' => 'My entire box was missing from the delivery.',
                'photo' => UploadedFile::fake()->image('damage.jpg', 800, 600),
            ])
            ->assertRedirect();

        $claim = Claim::where('delivery_id', $delivery->id)->first();
        $this->assertNotNull($claim);
        $this->assertNotNull($claim->photo_path);
        Storage::disk('public')->assertExists($claim->photo_path);
    }

    public function test_rejects_a_claim_with_a_short_description(): void
    {
        $user = User::factory()->create();
        $delivery = $this->makeDeliveryForUser($user);

        $this->actingAs($user)
            ->post(route('claims.store', $delivery), [
                'type' => 'damaged',
                'description' => 'Short',
            ])
            ->assertSessionHasErrors('description');
    }

    public function test_rejects_an_invalid_photo_mime_type(): void
    {
        $user = User::factory()->create();
        $delivery = $this->makeDeliveryForUser($user);

        $this->actingAs($user)
            ->post(route('claims.store', $delivery), [
                'type' => 'damaged',
                'description' => 'The items inside the box were severely damaged.',
                'photo' => UploadedFile::fake()->create('malware.pdf', 100, 'application/pdf'),
            ])
            ->assertSessionHasErrors('photo');
    }

    public function test_unauthorized_user_cannot_file_a_claim(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $delivery = $this->makeDeliveryForUser($owner);

        $this->actingAs($other)
            ->post(route('claims.store', $delivery), [
                'type' => 'damaged',
                'description' => 'Trying to claim someone else\'s delivery.',
            ])
            ->assertForbidden();
    }

    public function test_shows_a_claim_to_its_owner(): void
    {
        $user = User::factory()->create();
        $delivery = $this->makeDeliveryForUser($user);

        $claim = Claim::create([
            'id' => (string) Str::uuid(),
            'delivery_id' => $delivery->id,
            'type' => 'damaged',
            'description' => 'Box was crushed during shipping.',
            'status' => 'open',
        ]);

        $this->actingAs($user)
            ->get(route('claims.show', $claim))
            ->assertOk()
            ->assertSee('Claim Details');
    }

    public function test_prevents_another_user_from_viewing_a_claim(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $delivery = $this->makeDeliveryForUser($owner);

        $claim = Claim::create([
            'id' => (string) Str::uuid(),
            'delivery_id' => $delivery->id,
            'type' => 'missing',
            'description' => 'The box never arrived at the address.',
            'status' => 'open',
        ]);

        $this->actingAs($other)
            ->get(route('claims.show', $claim))
            ->assertForbidden();
    }

    public function test_guest_cannot_access_claims(): void
    {
        $user = User::factory()->create();
        $delivery = $this->makeDeliveryForUser($user);

        $this->get(route('claims.create', $delivery))
            ->assertRedirect(route('login'));
    }
}
