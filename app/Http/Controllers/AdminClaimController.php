<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResolveClaimRequest;
use App\Models\Claim;
use App\Services\ClaimResolutionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminClaimController extends Controller
{
    public function __construct(private ClaimResolutionService $claimResolutionService) {}

    public function index(Request $request): View
    {
        abort_unless($request->user()?->isAdmin(), 403);

        $search = trim((string) $request->string('q'));
        $status = trim((string) $request->string('status'));
        $type = trim((string) $request->string('type'));

        $claims = Claim::query()
            ->with(['delivery.address', 'subscription.user', 'resolvedBy'])
            ->when($search !== '', function ($query) use ($search): void {
                $query->where('description', 'like', "%{$search}%")
                    ->orWhereHas('subscription.user', fn ($userQuery) => $userQuery->where('email', 'like', "%{$search}%"));
            })
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->when($type !== '', fn ($query) => $query->where('type', $type))
            ->latest('submitted_at')
            ->get();

        return view('ops.claims.index', [
            'claims' => $claims,
            'filters' => [
                'q' => $search,
                'status' => $status,
                'type' => $type,
            ],
        ]);
    }

    public function resolve(ResolveClaimRequest $request, Claim $claim): RedirectResponse
    {
        $this->claimResolutionService->resolve($claim, $request->user(), $request->validated('resolution_notes'));

        return back()->with('status', 'Claim resolved.');
    }

    public function reject(ResolveClaimRequest $request, Claim $claim): RedirectResponse
    {
        $this->claimResolutionService->reject($claim, $request->user(), $request->validated('resolution_notes'));

        return back()->with('status', 'Claim rejected.');
    }
}
