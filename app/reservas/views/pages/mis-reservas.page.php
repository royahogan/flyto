<?php

$currentUser = is_array($currentUser ?? null) ? $currentUser : [];
$reservas = is_array($reservas ?? null) ? $reservas : [];
$estadoSeleccionado = (string) ($estadoSeleccionado ?? 'todas');
$basePath = $basePath ?? '';
$profileSection = 'reservas';

$html = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$nombreCompleto = trim((string) ($currentUser['nombre'] ?? '') . ' ' . (string) ($currentUser['apellido'] ?? ''));
$nombreCompleto = $nombreCompleto !== '' ? $nombreCompleto : 'Usuario Flyto';
$meses = [1 => 'ene.', 'feb.', 'mar.', 'abr.', 'may.', 'jun.', 'jul.', 'ago.', 'sept.', 'oct.', 'nov.', 'dic.'];
$fechaCorta = static function (\DateTime $fecha) use ($meses): string {
    return $fecha->format('d') . ' ' . $meses[(int) $fecha->format('n')] . ' ' . $fecha->format('Y');
};
$filtros = [
    'todas' => 'Todas',
    'confirmada' => 'Confirmadas',
    'completada' => 'Completadas',
    'cancelada' => 'Canceladas',
];

?>
<section class="bg-flyto-sand">
    <div class="bg-flyto-navy px-6 py-8 text-flyto-sand">
        <div class="mx-auto flex max-w-7xl items-start gap-4">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center bg-flyto-sand/10" aria-hidden="true">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none">
                    <path d="M12 12C14.2 12 16 10.2 16 8C16 5.8 14.2 4 12 4C9.8 4 8 5.8 8 8C8 10.2 9.8 12 12 12Z" stroke="currentColor" stroke-width="1.7"/>
                    <path d="M5 20C5.8 16.8 8.2 15.2 12 15.2C15.8 15.2 18.2 16.8 19 20" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                </svg>
            </span>
            <div>
                <p class="font-mono text-xs leading-4 text-flyto-sand/50">Cuenta de usuario</p>
                <h1 class="mt-0.5 font-display text-2xl font-medium leading-9"><?= $html($nombreCompleto) ?></h1>
                <p class="text-sm leading-5 text-flyto-sand/60"><?= $html($currentUser['email'] ?? '') ?></p>
            </div>
        </div>
    </div>

    <div class="mx-auto grid max-w-7xl gap-8 px-6 py-8 md:grid-cols-[259px_minmax(0,1fr)]">
        <?php require __DIR__ . '/../../../perfil/views/components/profile-sidebar.php'; ?>

        <div class="min-w-0">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <h2 class="font-display text-[20.8px] font-medium leading-8 text-flyto-ink">Mis reservas</h2>
                <nav class="flex max-w-full overflow-x-auto border border-flyto-ink/10 bg-white" aria-label="Filtrar reservas por estado">
                    <?php foreach ($filtros as $estado => $label): ?>
                        <?php $activo = $estadoSeleccionado === $estado; ?>
                        <a
                            href="<?= $html($basePath) ?>/mi-perfil/mis-reservas<?= $estado === 'todas' ? '' : '?estado=' . $html($estado) ?>"
                            class="whitespace-nowrap px-4 py-2 text-xs font-medium <?= $activo ? 'bg-flyto-navy text-flyto-sand' : 'text-flyto-muted hover:bg-flyto-sand' ?>"
                            <?= $activo ? 'aria-current="page"' : '' ?>
                        ><?= $html($label) ?></a>
                    <?php endforeach; ?>
                </nav>
            </div>

            <div class="mt-5 border-t border-flyto-ink/10">
                <?php if ($reservas === []): ?>
                    <div class="border border-t-0 border-flyto-ink/10 bg-white px-6 py-10 text-center">
                        <p class="text-sm text-flyto-muted">No hay reservas para el estado seleccionado.</p>
                    </div>
                <?php endif; ?>

                <?php foreach ($reservas as $reserva): ?>
                    <?php
                    $vuelo = $reserva->vuelo;
                    $estado = strtolower($reserva->estado);
                    $badge = match ($estado) {
                        'confirmada' => 'bg-flyto-navy/10 text-flyto-navy',
                        'completada' => 'bg-emerald-50 text-emerald-800',
                        'cancelada' => 'bg-red-50 text-red-800',
                        default => 'bg-flyto-sand text-flyto-muted',
                    };
                    ?>
                    <a
                        href="<?= $html($basePath) ?>/reservas/detalle?id=<?= (int) $reserva->id ?>"
                        class="grid gap-4 border border-t-0 border-flyto-ink/10 bg-white px-6 py-5 transition hover:bg-flyto-sand/50 lg:grid-cols-[minmax(180px,1fr)_minmax(120px,0.65fr)_72px_100px_84px_16px] lg:items-center"
                        aria-label="Ver detalle de la reserva <?= (int) $reserva->id ?>"
                    >
                        <div>
                            <div class="flex items-center gap-2 font-display text-base font-medium leading-6">
                                <span><?= $html($vuelo->ciudadOrigen['abreviacionCiudad'] ?? '') ?></span>
                                <svg class="h-3.5 w-3.5 text-flyto-muted" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                    <path d="M2 8H14M10.5 4.5L14 8L10.5 11.5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <span><?= $html($vuelo->ciudadDestino['abreviacionCiudad'] ?? '') ?></span>
                            </div>
                            <p class="mt-1.5 text-xs leading-4 text-flyto-muted"><?= $html($fechaCorta($vuelo->fechaSalida)) ?> &rarr; <?= $html($fechaCorta($vuelo->fechaLlegada)) ?></p>
                        </div>
                        <div class="text-xs leading-4">
                            <p class="text-flyto-ink"><?= $html($vuelo->aerolinea['nombreAerolinea'] ?? '') ?></p>
                            <p class="mt-0.5 text-flyto-muted"><?= $html($vuelo->codigoVuelo) ?></p>
                        </div>
                        <div class="text-xs leading-4">
                            <p class="text-flyto-muted">Pasajeros</p>
                            <p class="mt-0.5 text-flyto-ink"><?= count($reserva->pasajeros) ?></p>
                        </div>
                        <span class="inline-flex w-fit items-center justify-center px-2.5 py-1 text-xs font-medium <?= $badge ?>"><?= $html(ucfirst($estado)) ?></span>
                        <strong class="font-display text-base font-medium leading-6">USD <?= $html(number_format($reserva->precioTotal, 0, ',', '.')) ?></strong>
                        <svg class="hidden h-4 w-4 text-flyto-muted lg:block" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                            <path d="M6 3L11 8L6 13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
