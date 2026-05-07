<?php

namespace Tests\Feature;

use Illuminate\Support\ViewErrorBag;
use Tests\TestCase;

class ThemeModeRenderTest extends TestCase
{
    public function test_layout_renders_theme_toggle_controls(): void
    {
        view()->share('errors', new ViewErrorBag);

        $html = view('auth.login')->render();

        $this->assertStringContainsString('data-theme-toggle="light"', $html);
        $this->assertStringContainsString('data-theme-toggle="dark"', $html);
        $this->assertStringContainsString('data-theme-toggle="system"', $html);
    }

    public function test_theme_script_initialization_exists(): void
    {
        $script = file_get_contents(resource_path('js/app.js'));

        $this->assertNotFalse($script);
        $this->assertStringContainsString('applyTheme', $script);
        $this->assertStringContainsString('theme-toggle-btn-active', $script);
    }

    public function test_dashboard_hero_has_dark_mode_overrides(): void
    {
        $dashboardView = file_get_contents(resource_path('views/dashboard.blade.php'));

        $this->assertNotFalse($dashboardView);
        $this->assertStringContainsString('dark:bg-[linear-gradient(160deg,#172033_0%,#101a2d_56%,#0b1426_100%)]', $dashboardView);
        $this->assertStringContainsString('dark:bg-canvas/75', $dashboardView);
    }
}
