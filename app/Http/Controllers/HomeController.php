<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $plans = SubscriptionPlan::query()
            ->where('is_active', true)
            ->orderBy('price_monthly')
            ->get();

        return view('welcome', [
            'plans' => $plans,
        ]);
    }
}
