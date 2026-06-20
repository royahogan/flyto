<?php

require_once __DIR__ . '/../components/auth-ui.php';

flytoAuthShellStart('Recuperaci&oacute;n de cuenta', 'Recuper&aacute; tu contrase&ntilde;a');
?>
<form action="<?= flytoAuthUrl($basePath ?? '', '/recuperar-contrasena/codigo') ?>" method="get" class="mt-8 border border-flyto-ink/10 bg-white p-8">
    <p class="text-sm leading-[22.75px] text-flyto-muted">
        Ingres&aacute; tu correo electr&oacute;nico y te enviaremos un c&oacute;digo para restablecer tu contrase&ntilde;a.
    </p>

    <div class="mt-5">
        <?php flytoAuthField('Correo electr&oacute;nico', 'email', 'email', 'nombre@ejemplo.com', 'email'); ?>
    </div>

    <div class="mt-7 grid gap-3 sm:grid-cols-2">
        <a href="<?= flytoAuthUrl($basePath ?? '', '/auth/login') ?>" class="flex h-[45.6px] items-center justify-center gap-2 border border-flyto-ink/10 bg-white px-6 text-sm font-medium text-flyto-ink">
            <?= flytoBackIcon() ?> Volver
        </a>
        <button type="submit" class="min-h-[45.6px] bg-flyto-navy px-5 py-3 text-sm font-medium leading-5 text-flyto-sand">
            Enviar email de recuperaci&oacute;n
        </button>
    </div>
</form>
<?php flytoAuthShellEnd(); ?>
