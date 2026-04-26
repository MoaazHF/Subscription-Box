<?php

namespace App\Http\Controllers;

use App\Models\Box;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BoxController extends Controller
{
    /**
     * Display a listing of the subscriber's boxes.
     */
    public function index(Request $request): View
    {
        $boxes = Box::query()
            ->with('subscription')
            ->whereHas('subscription', function ($query) use ($request): void {
                $query->where('user_id', $request->user()->id);
            })->orderBy('period_year', 'desc')
            ->orderBy('period_month', 'desc')
            ->get();

        return view('boxes.index', compact('boxes'));
    }

    /**
     * Display the specified box with items.
     */
    public function show(Request $request, Box $box): View
    {
        $box->load(['items', 'subscription']);

        abort_unless($request->user()->isAdmin() || $box->ownedBy($request->user()), Response::HTTP_FORBIDDEN);

        return view('boxes.show', compact('box'));
    }
}
