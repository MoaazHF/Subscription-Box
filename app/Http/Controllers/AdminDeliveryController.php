<?php

namespace App\Http\Controllers;

use App\Models\Claim;
use App\Models\Delivery;
use App\Models\InventoryLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminDeliveryController extends Controller
{
    /**
     * Display all deliveries with filtering by status.
     */
    public function index(Request $request): View
    {
        $status = $request->query('status');

        $deliveries = Delivery::query()
            ->with(['address', 'driver', 'claims'])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->withCount('claims')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $statusCounts = collect(Delivery::ALL_STATUSES)
            ->mapWithKeys(fn ($s) => [$s => Delivery::where('status', $s)->count()]);

        return view('admin.deliveries.index', compact('deliveries', 'statusCounts', 'status'));
    }

    /**
     * Display a single delivery with full audit log.
     */
    public function show(Delivery $delivery): View
    {
        $delivery->load(['address', 'driver', 'box', 'claims', 'inventoryLogs']);

        return view('admin.deliveries.show', compact('delivery'));
    }

    /**
     * Admin force-update a delivery status (bypasses state machine restrictions).
     */
    public function update(Request $request, Delivery $delivery): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in(Delivery::ALL_STATUSES)],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $previousStatus = $delivery->status;
        $delivery->update(['status' => $validated['status']]);

        InventoryLog::create([
            'delivery_id' => $delivery->id,
            'event' => 'status_changed',
            'from_value' => $previousStatus,
            'to_value' => $validated['status'],
            'changed_by' => null,
            'changed_by_type' => 'admin',
            'notes' => $validated['notes'] ?? 'Admin override.',
        ]);

        return redirect()
            ->route('admin.deliveries.show', $delivery)
            ->with('success', 'Status updated to: '.ucfirst(str_replace('_', ' ', $delivery->status)));
    }

    /**
     * Admin update a claim status.
     */
    public function updateClaim(Request $request, Claim $claim): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in(['open', 'under_review', 'resolved', 'rejected'])],
        ]);

        $claim->update(['status' => $validated['status']]);

        return redirect()
            ->route('admin.deliveries.show', $claim->delivery_id)
            ->with('success', 'Claim status updated to: '.ucfirst(str_replace('_', ' ', $claim->status)));
    }
}
