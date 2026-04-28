<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use Illuminate\Contracts\View\View;

class SubscriptionPlanController extends Controller
{
    public function index(): View
    {
        return view('plans.index', [
            'plans' => SubscriptionPlan::query()
                ->where('is_active', true)
                ->orderBy('price_monthly')
                ->get(),
        ]);
    }
}
