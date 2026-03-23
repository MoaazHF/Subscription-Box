<?php

class UserController extends Controller
{
    private User $users;

    public function __construct()
    {
        $this->users = new User();
    }

    public function index(): void
    {
        Auth::requireRole('admin');

        $this->view('users/index', [
            'pageTitle' => 'Users',
            'users' => $this->users->all(),
        ]);
    }
}

