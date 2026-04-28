<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        $payments = Payment::query()
            ->whereHas('subscription', function ($query) use ($request): void {
                $query->whereBelongsTo($request->user());
            })
            ->with('subscription.plan')
            ->latest()
            ->paginate(10);

        return view('payments.index', [
            'payments' => $payments,
        ]);
    }
}
