<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class MediaController extends Controller
{
    public function show(Request $request): Response
    {
        $path = ltrim((string) $request->query('path', ''), '/');

        abort_if($path === '', Response::HTTP_NOT_FOUND);
        abort_if(str_contains($path, '..'), Response::HTTP_NOT_FOUND);

        $allowedPrefixes = ['products/', 'claims/'];
        $isAllowedPath = collect($allowedPrefixes)->contains(
            fn (string $prefix): bool => str_starts_with($path, $prefix)
        );

        abort_unless($isAllowedPath, Response::HTTP_NOT_FOUND);
        abort_unless(Storage::disk('public')->exists($path), Response::HTTP_NOT_FOUND);

        return Storage::disk('public')->response($path);
    }

    public function branding(string $file): Response
    {
        $allowedFiles = [
            'basic.png',
            'standrad.png',
            'premium.png',
            'AppIcon.png',
            'HeroSection.png',
            'cover.png',
            'login.png',
            'register.png',
            'WebSiteWalkthrough.gif',
            'Home[DarkMode].png',
            'Home[LightMode].png',
        ];

        abort_unless(in_array($file, $allowedFiles, true), Response::HTTP_NOT_FOUND);

        $filePath = public_path($file);
        abort_unless(is_file($filePath), Response::HTTP_NOT_FOUND);

        return response()->file($filePath);
    }
}
