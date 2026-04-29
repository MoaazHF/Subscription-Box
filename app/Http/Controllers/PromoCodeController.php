<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApplyPromoCodeRequest;
use App\Http\Requests\StorePromoCodeRequest;
use App\Models\PromoCode;
use App\Services\PromoCodeService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class PromoCodeController extends Controller
{
    public function __construct(private PromoCodeService $promoCodeService) {}

    public function index(): View
    {
        return view('promo-codes.index', [
            'promoCodes' => PromoCode::query()->latest('created_at')->paginate(20),
        ]);
    }

    public function store(StorePromoCodeRequest $request): RedirectResponse
    {
        $this->promoCodeService->create($request->user(), $request->validated());

        return back()->with('status', 'Promo code created.');
    }

    public function apply(ApplyPromoCodeRequest $request): RedirectResponse
    {
        $promoCode = PromoCode::query()
            ->where('code', strtoupper($request->validated('code')))
            ->firstOrFail();

        $this->promoCodeService->applyForUser($promoCode, $request->user());

        return back()->with('status', 'Promo code applied.');
    }
}
