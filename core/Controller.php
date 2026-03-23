<?php

abstract class Controller
{
    protected function view(string $view, array $data = []): void
    {
        extract($data);
        $viewPath = BASE_PATH . '/app/Views/' . $view . '.php';

        if (! file_exists($viewPath)) {
            http_response_code(500);
            echo 'View not found.';
            exit;
        }

        require BASE_PATH . '/app/Views/layouts/main.php';
    }

    protected function redirect(string $path = ''): void
    {
        redirect($path);
    }
}

