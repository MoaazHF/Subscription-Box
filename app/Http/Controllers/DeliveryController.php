<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeliveryController extends Controller
{
    /**
     * Display a listing of the deliveries for the subscriber.
     */
    public function index()
    {
        // Get deliveries belonging to the authenticated user through their addresses
        $deliveries = Delivery::whereHas('address', function ($query) {
            $query->where('user_id', Auth::id());
        })
        ->with('address') // Eager load address
        ->orderBy('created_at', 'desc')
        ->get();

        return view('deliveries.index', compact('deliveries'));
    }

    /**
     * Display the specified delivery.
     */
    public function show(Delivery $delivery)
    {
        // Ensure the delivery belongs to the authenticated user
        if ($delivery->address->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $delivery->load(['box', 'address', 'claims']);

        return view('deliveries.show', compact('delivery'));
    }
}
