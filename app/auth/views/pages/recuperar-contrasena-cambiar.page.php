<?php

require_once __DIR__ . '/../components/auth-ui.php';

flytoAuthShellStart('Cambio de contrase&ntilde;a', 'Cre&aacute; tu nueva contrase&ntilde;a');
?>
<form action="<?= flytoAuthUrl($basePath ?? '', '/auth/login') ?>" method="get" class="mt-8 border border-flyto-ink/10 bg-white p-8">
    <input type="hidden" name="login" value="recuperacion-pendiente">

    <div class="grid gap-4">
        <?php flytoAuthField('Nueva contrase&ntilde;a', 'password', 'password', '********', 'new-password'); ?>
        <?php flytoAuthField('Repetir contrase&ntilde;a', 'password_confirmation', 'password', '********', 'new-password'); ?>
    </div>

    <div class="mt-5">
        <?php flytoPasswordRequirements(); ?>
    </div>

    <div class="mt-7 grid gap-3 sm:grid-cols-2">
        <a href="<?= flytoAuthUrl($basePath ?? '', '/recuperar-contrasena/codigo') ?>" class="flex h-[45.6px] items-center justify-center gap-2 border border-flyto-ink/10 bg-white px-6 text-sm font-medium text-flyto-ink">
            <?= flytoBackIcon() ?> Volver
        </a>
        <button type="submit" class="h-[45.6px] bg-[#e5e4e0] px-5 text-sm font-medium text-flyto-muted">
            Cambiar contrase&ntilde;a
        </button>
    </div>
</form>
<?php flytoAuthShellEnd(); ?>
