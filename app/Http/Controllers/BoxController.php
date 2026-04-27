<?php

namespace App\Http\Controllers;

use App\Models\Box;
use Illuminate\Http\Request;

class BoxController extends Controller
{
    /**
     * Display a listing of the subscriber's boxes.
     */
    public function index(Request $request)
    {
        $boxes = Box::whereHas('subscription', function ($query) use ($request) {
            // Using optional() or ?? to prevent errors if we test without full auth setup initially
            $query->where('user_id', $request->user()?->id);
        })->orderBy('period_year', 'desc')
            ->orderBy('period_month', 'desc')
            ->get();

        return view('boxes.index', compact('boxes'));
    }

    /**
     * Display the specified box with items.
     */
    public function show(Box $box)
    {
        // Eager load items for the view
        $box->load('items');

        return view('boxes.show', compact('box'));
    }
}
