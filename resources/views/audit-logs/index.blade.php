@php($title = 'Audit Logs')

@extends('layouts.app')

@section('content')
    <section class="space-y-8">
        <div class="air-panel">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="air-kicker">Admin only</p>
                    <h1 class="air-title">Audit logs stay explicit and reviewable.</h1>
                    <p class="air-copy">Each critical operation writes a human-readable entry so customer and operational events remain traceable for support and compliance review.</p>
                </div>
                <span class="air-chip-dark">{{ $logs->total() }} total</span>
            </div>

            <div class="mt-8 space-y-4">
                @forelse ($logs as $log)
                    <article class="air-grid-card space-y-4">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                            <div class="space-y-3">
                                <div class="flex flex-wrap items-center gap-2">
                                    <p class="text-lg font-semibold tracking-[-0.01em] text-ink">{{ $log->action }}</p>
                                    <span class="air-chip">{{ $log->entity_type ?? 'N/A' }}{{ $log->entity_id ? ' #'.$log->entity_id : '' }}</span>
                                </div>
                                <p class="text-sm text-ash">User: {{ $log->user?->email ?? 'system' }}</p>
                            </div>

                            <div class="text-sm font-medium text-ash">
                                {{ $log->created_at?->format('M d, Y H:i') }}
                            </div>
                        </div>

                        @if (! empty($log->metadata))
                            <div class="rounded-[24px] border border-hairline bg-cloud p-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-mute">Metadata</p>
                                <pre class="mt-3 overflow-x-auto text-xs leading-6 text-focus">{{ json_encode($log->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                            </div>
                        @endif
                    </article>
                @empty
                    <div class="air-panel-soft">
                        <p class="text-sm text-ash">No audit entries yet.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $logs->links() }}
            </div>
        </div>
    </section>
@endsection
