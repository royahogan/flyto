<?php
$currentUser = $currentUser ?? null;
$currentUser = is_array($currentUser) ? $currentUser : [];
$basePath = $basePath ?? '';
$profileSection = 'datos';

$nombre = trim((string) ($currentUser['nombre'] ?? ''));
$apellido = trim((string) ($currentUser['apellido'] ?? ''));
$email = trim((string) ($currentUser['email'] ?? ''));
$nombreCompleto = trim($nombre . ' ' . $apellido);

if ($nombreCompleto === '') {
    $nombreCompleto = 'Usuario Flyto';
}

function flytoProfileText(string $value, string $fallback = '-'): string
{
    return htmlspecialchars($value !== '' ? $value : $fallback, ENT_QUOTES, 'UTF-8');
}

?>
<section class="bg-flyto-sand">
    <div class="bg-flyto-navy px-6 py-7 text-flyto-sand">
        <div class="mx-auto flex max-w-7xl items-start gap-4">
            <span class="flex h-12 w-12 items-center justify-center bg-flyto-sand/20 text-flyto-sand" aria-hidden="true">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none">
                    <path d="M12 12C14.2091 12 16 10.2091 16 8C16 5.79086 14.2091 4 12 4C9.79086 4 8 5.79086 8 8C8 10.2091 9.79086 12 12 12Z" stroke="currentColor" stroke-width="1.7"/>
                    <path d="M5 20C5.8 16.8 8.2 15.2 12 15.2C15.8 15.2 18.2 16.8 19 20" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                </svg>
            </span>
            <div>
                <p class="font-mono text-xs leading-4 text-flyto-sand/50">Cuenta de usuario</p>
                <h1 class="mt-1 font-display text-2xl font-medium leading-8 text-flyto-sand">
                    <?= flytoProfileText($nombreCompleto) ?>
                </h1>
                <p class="text-sm leading-5 text-flyto-sand/60"><?= flytoProfileText($email) ?></p>
            </div>
        </div>
    </div>

    <div class="mx-auto grid max-w-7xl gap-6 px-6 py-7 md:grid-cols-[259px_1fr]">
        <?php require __DIR__ . '/../components/profile-sidebar.php'; ?>

        <div>
            <?php require __DIR__ . '/../../../reservas/views/components/reserva-feedback.php'; ?>
            <h2 class="font-display text-[23px] font-medium leading-8 text-flyto-ink">Datos personales</h2>
            <section class="mt-5 border border-flyto-ink/10 bg-white" aria-labelledby="profile-account-info">
                <div class="border-b border-flyto-ink/10 px-6 py-4">
                    <p id="profile-account-info" class="font-mono text-xs uppercase leading-4 tracking-[1.2px] text-flyto-muted">
                        Informaci&oacute;n de la cuenta
                    </p>
                </div>
                <dl>
                    <div class="grid gap-2 border-b border-flyto-ink/10 px-6 py-4 md:grid-cols-[259px_1fr] md:items-center">
                        <dt class="font-mono text-[10.4px] uppercase leading-4 tracking-[0.26px] text-flyto-muted">Nombre</dt>
                        <dd class="text-sm leading-5 text-flyto-ink"><?= flytoProfileText($nombre) ?></dd>
                    </div>
                    <div class="grid gap-2 border-b border-flyto-ink/10 px-6 py-4 md:grid-cols-[259px_1fr] md:items-center">
                        <dt class="font-mono text-[10.4px] uppercase leading-4 tracking-[0.26px] text-flyto-muted">Apellido</dt>
                        <dd class="text-sm leading-5 text-flyto-ink"><?= flytoProfileText($apellido) ?></dd>
                    </div>
                    <div class="grid gap-2 px-6 py-4 md:grid-cols-[259px_1fr] md:items-center">
                        <dt class="font-mono text-[10.4px] uppercase leading-4 tracking-[0.26px] text-flyto-muted">Correo electr&oacute;nico</dt>
                        <dd class="text-sm leading-5 text-flyto-ink" style="overflow-wrap: anywhere;"><?= flytoProfileText($email) ?></dd>
                    </div>
                </dl>
            </section>
        </div>
    </div>
</section>
