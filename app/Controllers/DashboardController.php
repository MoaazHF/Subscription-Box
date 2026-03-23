<?php

class DashboardController extends Controller
{
    public function index(): void
    {
        Auth::requireLogin();

        $this->view('dashboard/index', [
            'pageTitle' => 'Dashboard',
            'user' => Auth::user(),
        ]);
    }
}

