<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSocialPostRequest;
use App\Models\SocialPost;
use App\Services\SocialPostService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SocialPostController extends Controller
{
    public function __construct(private SocialPostService $socialPostService) {}

    public function index(Request $request): View
    {
        $socialPosts = SocialPost::query()
            ->where('is_deleted', false)
            ->where(function ($query) use ($request): void {
                $query->where('visibility', 'public')
                    ->orWhere('user_id', $request->user()->id);
            })
            ->latest('created_at')
            ->get();

        return view('social-posts.index', [
            'socialPosts' => $socialPosts,
        ]);
    }

    public function store(StoreSocialPostRequest $request): RedirectResponse
    {
        $this->socialPostService->create($request->user(), $request->validated());

        return back()->with('status', 'Social post created.');
    }

    public function destroy(Request $request, SocialPost $socialPost): RedirectResponse
    {
        $this->socialPostService->softDelete($socialPost, $request->user());

        return back()->with('status', 'Social post deleted.');
    }
}
