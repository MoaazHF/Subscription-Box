<?php

function config(string $file): array
{
    static $configs = [];

    if (! isset($configs[$file])) {
        $path = BASE_PATH . '/config/' . $file . '.php';
        $configs[$file] = require $path;
    }

    return $configs[$file];
}

function app_url(string $path = ''): string
{
    $baseUrl = rtrim(config('app')['base_url'], '/');
    $path = trim($path, '/');

    return $path === '' ? $baseUrl : $baseUrl . '/' . $path;
}

function redirect(string $path = ''): void
{
    header('Location: ' . app_url($path));
    exit;
}

function old(string $key, string $default = ''): string
{
    $value = $_SESSION['_old'][$key] ?? $default;

    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

