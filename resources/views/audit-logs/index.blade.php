@extends('layouts.app')

@section('content')
    <section class="rounded-[2rem] border border-stone-200 bg-white p-8 shadow-sm">
        <p class="text-sm font-semibold uppercase tracking-[0.24em] text-amber-700">Admin only</p>
        <h1 class="mt-3 text-3xl font-black text-stone-900">Audit logs</h1>
        <p class="mt-2 text-sm leading-7 text-stone-600">Every Team 1 action writes a readable audit entry. This page is intentionally plain so review stays easy.</p>

        <div class="mt-8 space-y-4">
            @forelse ($logs as $log)
                <article class="rounded-[1.5rem] bg-stone-100 p-5">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <p class="text-sm font-semibold text-stone-900">{{ $log->action }}</p>
                            <p class="mt-1 text-sm text-stone-600">User: {{ $log->user?->email ?? 'system' }}</p>
                            <p class="mt-1 text-sm text-stone-600">Entity: {{ $log->entity_type ?? 'N/A' }} {{ $log->entity_id ? '#'.$log->entity_id : '' }}</p>
                            @if (! empty($log->metadata))
                                <pre class="mt-3 overflow-x-auto rounded-2xl bg-white p-3 text-xs text-stone-700">{{ json_encode($log->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                            @endif
                        </div>
                        <div class="text-sm text-stone-500">
                            {{ $log->created_at?->format('M d, Y H:i') }}
                        </div>
                    </div>
                </article>
            @empty
                <p class="rounded-2xl bg-stone-100 px-4 py-4 text-sm text-stone-600">No audit entries yet.</p>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $logs->links() }}
        </div>
    </section>
@endsection
