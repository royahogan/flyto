<?php

require_once __DIR__ . '/../components/auth-ui.php';

$flash = $flash ?? [];
$homeUrl = flytoAuthUrl($basePath ?? '', '/');
$loginUrl = flytoAuthUrl($basePath ?? '', '/auth/login');

if (!empty($flash['error'])) {
    $actions = '<a href="' . $homeUrl . '" class="flex h-11 items-center justify-center border border-flyto-ink/10 bg-white px-6 text-sm font-medium text-flyto-ink">Ir al inicio</a>';

    flytoStatusCard(
        '<path d="M6 6L18 18M18 6L6 18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>',
        'Verificaci&oacute;n no completada',
        'No pudimos confirmar tu cuenta',
        'El enlace no es v&aacute;lido o ya fue utilizado. Intent&aacute; iniciar sesi&oacute;n o solicit&aacute; un nuevo registro.',
        $actions
    );

    return;
}

$actions = '
    <div class="grid gap-3 sm:grid-cols-2">
        <a href="' . $homeUrl . '" class="flex h-[45.6px] items-center justify-center border border-flyto-ink/10 bg-white px-6 text-sm font-medium text-flyto-ink">Ir al inicio</a>
        <a href="' . $loginUrl . '" class="flex h-[45.6px] items-center justify-center bg-flyto-navy px-6 text-sm font-medium text-flyto-sand">Iniciar sesi&oacute;n</a>
    </div>';

flytoStatusCard(
    '<path d="M6 12L10 16L18 8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>',
    'Cuenta activada',
    'Tu cuenta ha sido confirmada',
    'Ya pod&eacute;s acceder a todas las funcionalidades de Flyto. Bienvenido a bordo.',
    $actions
);
