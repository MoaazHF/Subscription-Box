<?php
$appName = config('app')['name'];
$currentUser = Auth::user();
$flashSuccess = Session::flash('success');
$flashError = Session::flash('error');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(($pageTitle ?? 'Portal') . ' | ' . $appName) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?= e(app_url('assets/css/app.css')) ?>">
</head>
<body class="min-h-screen bg-slate-100 text-slate-900">
    <?php require BASE_PATH . '/app/Views/partials/navbar.php'; ?>

    <main class="mx-auto max-w-6xl px-4 py-8">
        <?php if ($flashSuccess): ?>
            <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                <?= e($flashSuccess) ?>
            </div>
        <?php endif; ?>

        <?php if ($flashError): ?>
            <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                <?= e($flashError) ?>
            </div>
        <?php endif; ?>

        <?php require $viewPath; ?>
    </main>
</body>
</html>

