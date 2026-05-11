<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ReportsModuleTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_admin_can_open_reports_and_export_csv(): void
    {
        $this->seed();

        $admin = User::query()->where('email', 'admin@example.com')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('reports.index', ['type' => 'subscriptions']))
            ->assertOk()
            ->assertSee('Reports Module');

        $this->actingAs($admin)
            ->get(route('reports.export-csv', ['type' => 'subscriptions']))
            ->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_non_admin_cannot_open_reports(): void
    {
        $this->seed();

        $subscriber = User::query()->where('email', 'test@example.com')->firstOrFail();

        $this->actingAs($subscriber)
            ->get(route('reports.index'))
            ->assertForbidden();
    }
}
