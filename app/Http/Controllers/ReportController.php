<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReportFilterRequest;
use App\Services\ReportsService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function __construct(private ReportsService $reportsService) {}

    public function index(ReportFilterRequest $request)
    {
        $type = $request->string('type')->toString() ?: 'subscriptions';
        $filters = $request->validated();
        $rows = $this->reportsService->rows($type, $filters);

        return view('reports.index', [
            'type' => $type,
            'rows' => $rows,
            'filters' => $filters,
            'headings' => $this->reportsService->headings($type),
            'types' => ['subscriptions', 'deliveries', 'claims', 'payments', 'notifications'],
        ]);
    }

    public function exportCsv(ReportFilterRequest $request, string $type): StreamedResponse
    {
        $headings = $this->reportsService->headings($type);
        $rows = $this->reportsService->rows($type, $request->validated());

        $filename = 'report-'.$type.'-'.now()->format('Ymd_His').'.csv';

        return response()->streamDownload(function () use ($headings, $rows, $type): void {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, $headings);

            foreach ($rows as $row) {
                fputcsv($handle, $this->mapRow($type, $row));
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    private function mapRow(string $type, object $row): array
    {
        return match ($type) {
            'subscriptions' => [
                $row->id,
                $row->user?->email,
                $row->plan?->name,
                $row->status,
                $row->auto_renew ? '1' : '0',
                $row->start_date,
                $row->next_billing_date,
            ],
            'deliveries' => [
                $row->id,
                $row->tracking_number,
                $row->status,
                $row->box?->subscription?->user?->email,
                $row->estimated_delivery,
                $row->eco_dispatch ? '1' : '0',
            ],
            'claims' => [
                $row->id,
                $row->type,
                $row->status,
                $row->delivery_id,
                $row->subscription_id,
                $row->submitted_at,
                $row->resolved_at,
            ],
            'payments' => [
                $row->id,
                $row->subscription_id,
                $row->amount,
                $row->tax_amount,
                $row->status,
                $row->gateway_ref,
                $row->created_at,
            ],
            'notifications' => [
                $row->id,
                $row->user_id,
                $row->type,
                $row->event_type,
                $row->status,
                $row->channel,
                $row->retry_count,
                $row->sent_at,
            ],
            default => [],
        };
    }
}
