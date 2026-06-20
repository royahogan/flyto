<?php

require_once __DIR__ . '/../components/auth-ui.php';

$loginFlash = $flash ?? [];
$loginOldInput = $oldInput ?? [];
$loginValidationErrors = $validationErrors ?? [];
$loginValue = fn (string $key): string => (string) ($loginOldInput[$key] ?? '');
$loginError = fn (string $key): string => (string) ($loginValidationErrors[$key] ?? '');

flytoAuthShellStart('Acceso a tu cuenta', 'Inici&aacute; sesi&oacute;n en Flyto');
?>
<form action="<?= flytoAuthUrl($basePath ?? '', '/auth/login') ?>" method="post" class="mt-8 border border-flyto-ink/10 bg-white p-8">
    <?php if (!empty($loginFlash['success'])): ?>
        <p class="mb-5 border border-flyto-navy bg-flyto-navy/10 px-4 py-3 text-sm leading-5 text-flyto-navy">
            <?= htmlspecialchars((string) $loginFlash['success'], ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php elseif (!empty($loginFlash['error'])): ?>
        <p class="mb-5 border border-flyto-ink/10 bg-flyto-sand px-4 py-3 text-sm leading-5 text-flyto-ink">
            <?= htmlspecialchars((string) $loginFlash['error'], ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php endif; ?>

    <?php if ($loginError('general') !== ''): ?>
        <p class="mb-5 border border-flyto-ink/10 bg-white px-4 py-3 text-sm leading-5 text-flyto-ink">
            <?= htmlspecialchars($loginError('general'), ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php endif; ?>

    <div class="grid gap-4">
        <?php flytoAuthField('Correo electr&oacute;nico', 'email', 'email', 'nombre@ejemplo.com', 'email', '', $loginValue('email'), $loginError('email')); ?>
        <?php flytoAuthField('Contrase&ntilde;a', 'password', 'password', '********', 'current-password', '', '', $loginError('password')); ?>
    </div>

    <a href="<?= flytoAuthUrl($basePath ?? '', '/recuperar-contrasena') ?>" class="mt-5 inline-block text-xs font-medium leading-4 text-flyto-navy">
        Olvid&eacute; mi contrase&ntilde;a
    </a>

    <div class="mt-6 grid gap-3">
        <button type="submit" class="h-11 w-full bg-flyto-navy px-6 text-sm font-medium text-flyto-sand">
            Iniciar sesi&oacute;n
        </button>
        <a href="<?= flytoAuthUrl($basePath ?? '', '/auth/registro') ?>" class="flex h-[45.6px] items-center justify-center border border-flyto-ink/10 bg-white px-6 text-sm font-medium text-flyto-ink">
            Registrarse
        </a>
    </div>
</form>
<?php flytoAuthShellEnd(); ?>
