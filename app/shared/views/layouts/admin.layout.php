<?php

$pageTitle = $pageTitle ?? 'Admin - Flyto';
$content = $content ?? '';
$basePath = $basePath ?? '';

?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>/assets/css/app.css">
</head>
<body class="bg-flyto-sand text-flyto-ink">
    <header class="bg-flyto-navy px-6 py-5 text-flyto-sand">
        <div class="mx-auto max-w-7xl">
            <h1 class="font-display text-3xl font-medium">Admin</h1>
        </div>
    </header>
    <main id="contenido-principal">
        <?= $content ?>
    </main>
</body>
</html>
