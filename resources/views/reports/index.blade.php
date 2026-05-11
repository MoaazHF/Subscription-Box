@php($title = 'Reports')

@extends('layouts.app')

@section('content')
    <section class="space-y-8">
        <div class="air-panel">
            <p class="air-kicker">Admin reporting</p>
            <h1 class="air-title">Reports Module</h1>
            <p class="air-copy">Generate printable reports and CSV exports for subscriptions, deliveries, claims, payments, and notifications.</p>
        </div>

        <div class="air-panel">
            <form method="GET" action="{{ route('reports.index') }}" class="grid gap-3 md:grid-cols-6">
                <select name="type" class="air-select md:col-span-1">
                    @foreach ($types as $reportType)
                        <option value="{{ $reportType }}" @selected($type === $reportType)>{{ ucfirst($reportType) }}</option>
                    @endforeach
                </select>
                <input name="q" type="text" class="air-input md:col-span-2" placeholder="Search" value="{{ $filters['q'] ?? '' }}">
                <input name="status" type="text" class="air-input md:col-span-1" placeholder="Status" value="{{ $filters['status'] ?? '' }}">
                <input name="from" type="date" class="air-input" value="{{ $filters['from'] ?? '' }}">
                <input name="to" type="date" class="air-input" value="{{ $filters['to'] ?? '' }}">
                <button type="submit" class="air-button-primary md:col-span-2">Apply</button>
                <a href="{{ route('reports.export-csv', ['type' => $type] + request()->query()) }}" class="air-button-secondary md:col-span-2">Export CSV</a>
                <button type="button" class="air-button-secondary md:col-span-2" onclick="window.print()">Printable View</button>
            </form>
        </div>

        <div class="air-panel overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-hairline">
                        @foreach ($headings as $heading)
                            <th class="px-3 py-2 text-left font-semibold text-ink">{{ $heading }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $row)
                        <tr class="border-b border-hairline/60">
                            @if ($type === 'subscriptions')
                                <td class="px-3 py-2">{{ $row->id }}</td>
                                <td class="px-3 py-2">{{ $row->user?->email }}</td>
                                <td class="px-3 py-2">{{ $row->plan?->name }}</td>
                                <td class="px-3 py-2">{{ $row->status }}</td>
                                <td class="px-3 py-2">{{ $row->auto_renew ? '1' : '0' }}</td>
                                <td class="px-3 py-2">{{ $row->start_date }}</td>
                                <td class="px-3 py-2">{{ $row->next_billing_date }}</td>
                            @elseif ($type === 'deliveries')
                                <td class="px-3 py-2">{{ $row->id }}</td>
                                <td class="px-3 py-2">{{ $row->tracking_number }}</td>
                                <td class="px-3 py-2">{{ $row->status }}</td>
                                <td class="px-3 py-2">{{ $row->box?->subscription?->user?->email }}</td>
                                <td class="px-3 py-2">{{ $row->estimated_delivery }}</td>
                                <td class="px-3 py-2">{{ $row->eco_dispatch ? '1' : '0' }}</td>
                            @elseif ($type === 'claims')
                                <td class="px-3 py-2">{{ $row->id }}</td>
                                <td class="px-3 py-2">{{ $row->type }}</td>
                                <td class="px-3 py-2">{{ $row->status }}</td>
                                <td class="px-3 py-2">{{ $row->delivery_id }}</td>
                                <td class="px-3 py-2">{{ $row->subscription_id }}</td>
                                <td class="px-3 py-2">{{ $row->submitted_at }}</td>
                                <td class="px-3 py-2">{{ $row->resolved_at }}</td>
                            @elseif ($type === 'payments')
                                <td class="px-3 py-2">{{ $row->id }}</td>
                                <td class="px-3 py-2">{{ $row->subscription_id }}</td>
                                <td class="px-3 py-2">{{ $row->amount }}</td>
                                <td class="px-3 py-2">{{ $row->tax_amount }}</td>
                                <td class="px-3 py-2">{{ $row->status }}</td>
                                <td class="px-3 py-2">{{ $row->gateway_ref }}</td>
                                <td class="px-3 py-2">{{ $row->created_at }}</td>
                            @else
                                <td class="px-3 py-2">{{ $row->id }}</td>
                                <td class="px-3 py-2">{{ $row->user_id }}</td>
                                <td class="px-3 py-2">{{ $row->type }}</td>
                                <td class="px-3 py-2">{{ $row->event_type }}</td>
                                <td class="px-3 py-2">{{ $row->status }}</td>
                                <td class="px-3 py-2">{{ $row->channel }}</td>
                                <td class="px-3 py-2">{{ $row->retry_count }}</td>
                                <td class="px-3 py-2">{{ $row->sent_at }}</td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td class="px-3 py-4 text-ash" colspan="{{ count($headings) }}">No report rows matched the current filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
