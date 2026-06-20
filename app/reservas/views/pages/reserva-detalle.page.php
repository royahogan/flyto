<?php

use App\Reservas\Models\Reserva;

/** @var Reserva|null $reserva */
$reserva = $reserva ?? null;
$error = $error ?? null;
$flash = $flash ?? [];
$puedeCancelar = $puedeCancelar ?? false;
$basePath = $basePath ?? '';
$html = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

$meses = [1 => 'ene.', 'feb.', 'mar.', 'abr.', 'may.', 'jun.', 'jul.', 'ago.', 'sept.', 'oct.', 'nov.', 'dic.'];
$fechaCorta = static fn (\DateTime $fecha): string => $fecha->format('d') . ' ' . $meses[(int) $fecha->format('n')] . ' ' . $fecha->format('Y');
$formatMoney = static fn (float $amount): string => number_format($amount, 0, ',', '.');

if ($reserva !== null) {
    $vuelo = $reserva->vuelo;
    $codigoReserva = sprintf('FLY-%s-%06d', $reserva->fechaReserva->format('Y'), (int) $reserva->id);
    $cantidadPasajeros = count($reserva->pasajeros);
    $estado = strtolower($reserva->estado);
    $estadoLabel = match ($estado) {
        'confirmada' => 'Confirmado',
        'cancelada' => 'Cancelado',
        'completada' => 'Completado',
        default => ucfirst($estado),
    };
    $estadoClass = match ($estado) {
        'confirmada' => 'bg-flyto-navy/10 text-flyto-navy',
        'cancelada' => 'bg-red-50 text-red-800',
        'completada' => 'bg-emerald-50 text-emerald-800',
        default => 'bg-flyto-ink/10 text-flyto-muted',
    };
}

?>
<section class="min-h-[426px] bg-flyto-sand px-6 py-8 text-flyto-ink">
    <div class="mx-auto max-w-[944px]">
        <a href="<?= $html($basePath) ?>/mi-perfil/mis-reservas" class="inline-flex items-center gap-2 text-sm font-medium text-flyto-muted transition hover:text-flyto-ink">
            <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                <path d="M10 3L5 8L10 13" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Volver a mis reservas
        </a>

        <?php if ($error !== null || $reserva === null): ?>
            <div class="mt-6 border border-flyto-ink/10 bg-white px-6 py-10 text-center">
                <p class="font-display text-2xl font-medium">No pudimos mostrar la reserva</p>
                <p class="mt-2 text-sm text-flyto-muted"><?= $html($error ?? 'Reserva no encontrada.') ?></p>
            </div>
        <?php else: ?>
            <?php if (!empty($flash['success'])): ?>
                <div class="mt-6 border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="status">
                    <?= $html($flash['success']) ?>
                </div>
            <?php elseif (!empty($flash['error'])): ?>
                <div class="mt-6 border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
                    <?= $html($flash['error']) ?>
                </div>
            <?php endif; ?>

            <header class="mt-5 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <p class="font-mono text-xs uppercase tracking-[0.3px] text-flyto-muted">Reserva</p>
                    <h1 class="mt-1 font-display text-2xl font-medium leading-9"><?= $html($codigoReserva) ?></h1>
                </div>
                <span class="inline-flex w-fit px-2.5 py-1 font-mono text-xs font-medium <?= $estadoClass ?>"><?= $html($estadoLabel) ?></span>
            </header>

            <div class="mt-6 grid gap-5 <?= $puedeCancelar ? 'lg:grid-cols-[minmax(0,2.4fr)_minmax(210px,1fr)]' : '' ?>">
                <article class="border border-flyto-ink/10 bg-white" aria-labelledby="informacion-vuelo-title">
                    <div class="border-b border-flyto-ink/10 px-6 py-4">
                        <h2 id="informacion-vuelo-title" class="font-mono text-xs uppercase tracking-[0.3px] text-flyto-muted">Información del vuelo</h2>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-[auto_minmax(72px,1fr)_auto] items-center gap-4">
                            <div>
                                <p class="font-display text-[30px] font-medium leading-[30px]"><?= $html($vuelo->ciudadOrigen['abreviacionCiudad'] ?? '') ?></p>
                                <p class="mt-1 text-xs leading-4 text-flyto-muted"><?= $html($vuelo->ciudadOrigen['nombreCiudad'] ?? '') ?></p>
                            </div>

                            <div class="flex items-center gap-2 text-flyto-muted" aria-hidden="true">
                                <span class="h-px flex-1 bg-flyto-ink/15"></span>
                                <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M21.5 16.5v2l-8-1.5v4l2 1.5V24L12 23l-3.5 1v-1.5l2-1.5v-4l-8 1.5v-2l8-4V7a1.5 1.5 0 0 1 3 0v5.5l8 4Z" transform="rotate(90 12 12)"/>
                                </svg>
                                <span class="h-px flex-1 bg-flyto-ink/15"></span>
                            </div>

                            <div class="text-right">
                                <p class="font-display text-[30px] font-medium leading-[30px]"><?= $html($vuelo->ciudadDestino['abreviacionCiudad'] ?? '') ?></p>
                                <p class="mt-1 text-xs leading-4 text-flyto-muted"><?= $html($vuelo->ciudadDestino['nombreCiudad'] ?? '') ?></p>
                            </div>
                        </div>

                        <dl class="mt-6 grid grid-cols-2 gap-x-4 gap-y-4 sm:grid-cols-3">
                            <div>
                                <dt class="font-mono text-[10.4px] uppercase tracking-[0.26px] text-flyto-muted">Fecha de salida</dt>
                                <dd class="mt-0.5 text-sm leading-5"><?= $html($fechaCorta($vuelo->fechaSalida)) ?></dd>
                            </div>
                            <div>
                                <dt class="font-mono text-[10.4px] uppercase tracking-[0.26px] text-flyto-muted">Aerolínea</dt>
                                <dd class="mt-0.5 text-sm leading-5"><?= $html($vuelo->aerolinea['nombreAerolinea'] ?? '') ?></dd>
                            </div>
                            <div>
                                <dt class="font-mono text-[10.4px] uppercase tracking-[0.26px] text-flyto-muted">Pasajeros</dt>
                                <dd class="mt-0.5 text-sm leading-5"><?= $cantidadPasajeros ?></dd>
                            </div>
                            <div>
                                <dt class="font-mono text-[10.4px] uppercase tracking-[0.26px] text-flyto-muted">N° de vuelo</dt>
                                <dd class="mt-0.5 text-sm leading-5"><?= $html($vuelo->codigoVuelo) ?></dd>
                            </div>
                            <div>
                                <dt class="font-mono text-[10.4px] uppercase tracking-[0.26px] text-flyto-muted">Total pagado</dt>
                                <dd class="mt-0.5 text-sm leading-5">USD <?= $html($formatMoney($reserva->precioTotal)) ?></dd>
                            </div>
                        </dl>
                    </div>
                </article>

                <?php if ($puedeCancelar): ?>
                    <aside class="border border-flyto-ink/10 bg-white" aria-labelledby="acciones-reserva-title">
                        <div class="border-b border-flyto-ink/10 px-5 py-4">
                            <h2 id="acciones-reserva-title" class="font-mono text-xs uppercase tracking-[0.3px] text-flyto-muted">Acciones</h2>
                        </div>
                        <form class="p-5" method="post" action="<?= $html($basePath) ?>/reservas/cancelar">
                            <input type="hidden" name="reservaId" value="<?= (int) $reserva->id ?>">
                            <button type="submit" class="flex h-9 w-full items-center border border-red-200 px-3.5 text-left text-sm font-medium text-red-600 transition hover:bg-red-50">
                                Cancelar reserva
                            </button>
                        </form>
                    </aside>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
