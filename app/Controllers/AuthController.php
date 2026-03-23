<?php

class AuthController extends Controller
{
    private User $users;

    public function __construct()
    {
        $this->users = new User();
    }

    public function showLogin(): void
    {
        if (Auth::check()) {
            $this->redirect('dashboard');
        }

        $this->view('auth/login', [
            'pageTitle' => 'Login',
        ]);
    }

    public function login(): void
    {
        Session::keepOldInput($_POST);

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $user = $this->users->findByEmail($email);

        if (! $user || ! password_verify($password, $user['password'])) {
            Session::flash('error', 'Invalid email or password.');
            $this->redirect('login');
        }

        Session::clearOldInput();
        Auth::login($user);
        Session::flash('success', 'Welcome back, ' . $user['name'] . '.');

        $this->redirect('dashboard');
    }

    public function showRegister(): void
    {
        if (Auth::check()) {
            $this->redirect('dashboard');
        }

        $this->view('auth/register', [
            'pageTitle' => 'Register',
        ]);
    }

    public function register(): void
    {
        Session::keepOldInput($_POST);

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if ($name === '' || $email === '' || $password === '') {
            Session::flash('error', 'All fields are required.');
            $this->redirect('register');
        }

        if ($password !== $confirmPassword) {
            Session::flash('error', 'Passwords do not match.');
            $this->redirect('register');
        }

        if ($this->users->findByEmail($email)) {
            Session::flash('error', 'Email already exists.');
            $this->redirect('register');
        }

        $this->users->create([
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role' => 'customer',
        ]);

        Session::clearOldInput();
        Session::flash('success', 'Account created successfully. Please login.');
        $this->redirect('login');
    }

    public function logout(): void
    {
        Auth::logout();
        Session::flash('success', 'You logged out successfully.');
        $this->redirect();
    }
}

