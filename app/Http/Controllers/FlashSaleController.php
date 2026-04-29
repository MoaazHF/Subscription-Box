<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFlashSaleRequest;
use App\Models\FlashSale;
use App\Services\FlashSaleService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FlashSaleController extends Controller
{
    public function __construct(private FlashSaleService $flashSaleService) {}

    public function index(): View
    {
        return view('flash-sales.index', [
            'flashSales' => FlashSale::query()->latest('created_at')->get(),
        ]);
    }

    public function store(StoreFlashSaleRequest $request): RedirectResponse
    {
        FlashSale::create([
            ...$request->validated(),
            'created_by' => $request->user()->id,
            'claimed_count' => 0,
            'created_at' => now(),
        ]);

        return back()->with('status', 'Flash sale created.');
    }

    public function claim(Request $request, FlashSale $flashSale): RedirectResponse
    {
        $this->flashSaleService->claim($flashSale->load('plan'), $request->user());

        return back()->with('status', 'Flash sale claimed.');
    }
}
