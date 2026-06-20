<?php

require_once __DIR__ . '/../components/auth-ui.php';

$registroFlash = $flash ?? [];
$registroOldInput = $oldInput ?? [];
$registroValidationErrors = $validationErrors ?? [];
$registroValue = fn (string $key): string => (string) ($registroOldInput[$key] ?? '');
$registroError = fn (string $key): string => (string) ($registroValidationErrors[$key] ?? '');

flytoAuthShellStart('Registro de usuario', 'Cre&aacute; tu cuenta en Flyto');
?>
<form action="<?= flytoAuthUrl($basePath ?? '', '/auth/registro') ?>" method="post" class="mt-8 border border-flyto-ink/10 bg-white p-8">
    <?php if (!empty($registroFlash['success'])): ?>
        <p class="mb-5 border border-flyto-navy bg-flyto-navy/10 px-4 py-3 text-sm leading-5 text-flyto-navy">
            <?= htmlspecialchars((string) $registroFlash['success'], ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php elseif (!empty($registroFlash['error'])): ?>
        <p class="mb-5 border border-flyto-ink/10 bg-flyto-sand px-4 py-3 text-sm leading-5 text-flyto-ink">
            <?= htmlspecialchars((string) $registroFlash['error'], ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php endif; ?>

    <?php if ($registroError('general') !== ''): ?>
        <p class="mb-5 border border-flyto-ink/10 bg-white px-4 py-3 text-sm leading-5 text-flyto-ink">
            <?= htmlspecialchars($registroError('general'), ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php endif; ?>

    <div class="grid gap-4">
        <div class="grid gap-4 sm:grid-cols-2">
            <?php flytoAuthField('Nombre', 'nombre', 'text', 'Maria', 'given-name', '', $registroValue('nombre'), $registroError('nombre')); ?>
            <?php flytoAuthField('Apellido', 'apellido', 'text', 'Gonzalez', 'family-name', '', $registroValue('apellido'), $registroError('apellido')); ?>
        </div>
        <?php flytoAuthField('Correo electr&oacute;nico', 'email', 'email', 'nombre@ejemplo.com', 'email', '', $registroValue('email'), $registroError('email')); ?>
        <?php flytoAuthField('Tel&eacute;fono', 'telefono', 'tel', '1123456789', 'tel', '', $registroValue('telefono'), $registroError('telefono')); ?>
        <?php flytoAuthField('Contrase&ntilde;a', 'password', 'password', '********', 'new-password', '', '', $registroError('password')); ?>
        <?php flytoAuthField('Repetir contrase&ntilde;a', 'password_confirmation', 'password', '********', 'new-password', '', '', $registroError('password_confirmation')); ?>
    </div>

    <div class="mt-5">
        <?php flytoPasswordRequirements(); ?>
    </div>

    <button type="submit" class="mt-5 h-11 w-full bg-flyto-navy px-6 text-sm font-medium text-flyto-sand">
        Registrarse
    </button>

    <p class="mt-5 text-center text-xs leading-6 text-flyto-muted">
        &iquest;Ya ten&eacute;s una cuenta?
        <a href="<?= flytoAuthUrl($basePath ?? '', '/auth/login') ?>" class="text-base font-medium text-flyto-navy">Inici&aacute; sesi&oacute;n</a>
    </p>
</form>
<?php flytoAuthShellEnd(); ?>
