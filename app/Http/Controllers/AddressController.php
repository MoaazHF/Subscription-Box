<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressRequest;
use App\Models\Address;
use App\Services\AuditLogService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function __construct(
        private AuditLogService $auditLogService
    ) {
    }

    public function index(Request $request): View
    {
        return view('addresses.index', [
            'addresses' => $request->user()->addresses()->latest()->get(),
        ]);
    }

    public function store(AddressRequest $request): RedirectResponse
    {
        $payload = $this->normalizedPayload($request);
        $hasAddresses = $request->user()->addresses()->exists();

        if (! $hasAddresses) {
            $payload['is_default'] = true;
        }

        if ($payload['is_default']) {
            $request->user()->addresses()->update(['is_default' => false]);
        }

        $address = $request->user()->addresses()->create($payload);

        $this->auditLogService->record($request->user(), 'address.created', $address, $payload, $request->ip());

        return back()->with('status', 'Address saved.');
    }

    public function update(AddressRequest $request, Address $address): RedirectResponse
    {
        $this->ensureOwnership($request, $address);

        $payload = $this->normalizedPayload($request);

        if ($payload['is_default']) {
            $request->user()->addresses()->update(['is_default' => false]);
        }

        $address->update($payload);

        $this->auditLogService->record($request->user(), 'address.updated', $address, $payload, $request->ip());

        return back()->with('status', 'Address updated.');
    }

    public function destroy(Request $request, Address $address): RedirectResponse
    {
        $this->ensureOwnership($request, $address);

        $address->delete();

        $this->auditLogService->record($request->user(), 'address.deleted', $address, [], $request->ip());

        return back()->with('status', 'Address deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    private function normalizedPayload(AddressRequest $request): array
    {
        $payload = $request->validated();
        $payload['country'] = strtoupper($payload['country']);
        $payload['is_default'] = (bool) ($payload['is_default'] ?? false);

        return $payload;
    }

    private function ensureOwnership(Request $request, Address $address): void
    {
        abort_unless($address->user_id === $request->user()->id, 403);
    }
}
