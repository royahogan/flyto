<?php

use App\Reservas\Models\Reserva;

/** @var Reserva $reserva */
/** @var string $codigoReserva */
/** @var string $correoConfirmacion */
$basePath = $basePath ?? '';
$html = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$vuelo = $reserva->vuelo;
$cantidadPasajeros = count($reserva->pasajeros);
$formatMoney = static fn (float $amount): string => number_format($amount, 0, ',', '.');
$estado = ucfirst(strtolower($reserva->estado));
$pasajerosLabel = $cantidadPasajeros === 1 ? '1 pasajero' : $cantidadPasajeros . ' pasajeros';
$steps = ['Selección', 'Pasajeros', 'Pago', 'Confirmación'];
?>

<section class="min-h-[954px] bg-flyto-sand pb-16 text-flyto-ink">
    <div class="border-b border-flyto-ink/10 bg-white">
        <ol class="mx-auto grid max-w-5xl grid-cols-2 gap-y-3 px-6 py-3 sm:grid-cols-4" aria-label="Progreso de la reserva">
            <?php foreach ($steps as $index => $step): ?>
                <?php $isCurrent = $index === 3; ?>
                <li class="flex items-center gap-3 text-xs <?= $isCurrent ? 'font-medium text-flyto-ink' : 'text-flyto-muted' ?>" <?= $isCurrent ? 'aria-current="step"' : '' ?>>
                    <span class="flex h-6 w-6 shrink-0 items-center justify-center bg-flyto-navy font-mono text-xs font-medium text-flyto-sand">
                        <?php if ($isCurrent): ?>
                            4
                        <?php else: ?>
                            <svg class="h-3 w-3" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m2.5 6 2.2 2.2L9.5 3.5"/></svg>
                        <?php endif; ?>
                    </span>
                    <span><?= $html($step) ?></span>
                    <?php if ($index < 3): ?><span class="ml-auto hidden h-px flex-1 bg-flyto-navy sm:block"></span><?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ol>
    </div>

    <div class="mx-auto max-w-[720px] px-6 pt-16">
        <header class="text-center">
            <span class="mx-auto flex h-14 w-14 items-center justify-center bg-flyto-navy text-flyto-sand" aria-hidden="true">
                <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m5 12 4 4L19 6"/></svg>
            </span>
            <h1 class="mt-5 font-display text-[32px] font-medium leading-[48px]">¡Reserva confirmada!</h1>
            <p class="mt-1 text-sm leading-5 text-flyto-muted">
                Recibirás una confirmación en
                <strong class="font-normal text-flyto-ink"><?= $html($correoConfirmacion) ?></strong>
            </p>
        </header>

        <article class="mt-10 border border-flyto-ink/10 bg-white" aria-label="Detalle de la reserva confirmada">
            <div class="flex min-h-[78px] items-center justify-between gap-4 border-b border-flyto-ink/10 px-6 py-4">
                <div>
                    <p class="font-mono text-xs uppercase tracking-[0.3px] text-flyto-muted">Nº de reserva</p>
                    <p class="mt-0.5 font-mono text-lg font-medium leading-7"><?= $html($codigoReserva) ?></p>
                </div>
                <span class="bg-flyto-navy/10 px-2.5 py-1 font-mono text-xs font-medium text-flyto-navy"><?= $html($estado) ?></span>
            </div>

            <div class="grid md:grid-cols-2">
                <div class="p-6 md:border-r md:border-flyto-ink/10">
                    <p class="font-mono text-xs uppercase tracking-[0.3px] text-flyto-muted">Vuelo de ida</p>

                    <div class="mt-3 flex items-center gap-3">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center bg-[#005b99] font-mono text-xs font-medium text-white">
                            <?= $html($vuelo->aerolinea['codigoIataAerolinea']) ?>
                        </span>
                        <div>
                            <p class="text-xs font-medium leading-4"><?= $html($vuelo->aerolinea['nombreAerolinea']) ?></p>
                            <p class="font-mono text-xs leading-4 text-flyto-muted"><?= $html($vuelo->codigoVuelo) ?></p>
                        </div>
                    </div>

                    <div class="mt-3 grid grid-cols-[auto_minmax(80px,1fr)_auto] items-center gap-3">
                        <div>
                            <p class="font-display text-xl font-medium leading-7"><?= $html($vuelo->fechaSalida->format('H:i')) ?></p>
                            <p class="font-mono text-xs text-flyto-muted"><?= $html($vuelo->ciudadOrigen['abreviacionCiudad']) ?></p>
                        </div>
                        <div class="text-center">
                            <p class="font-mono text-xs text-flyto-muted"><?= $html($vuelo->duracionTexto()) ?></p>
                            <div class="mt-2 flex items-center" aria-hidden="true">
                                <span class="h-px flex-1 bg-flyto-ink/15"></span>
                                <svg class="mx-1 h-3 w-3 rotate-90 text-flyto-muted" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2 8 9H3l-1 2 6 3v5l-2 2v1l6-1 6 1v-1l-2-2v-5l6-3-1-2h-5l-4-7Z"/></svg>
                                <span class="h-px flex-1 bg-flyto-ink/15"></span>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-display text-xl font-medium leading-7"><?= $html($vuelo->fechaLlegada->format('H:i')) ?></p>
                            <p class="font-mono text-xs text-flyto-muted"><?= $html($vuelo->ciudadDestino['abreviacionCiudad']) ?></p>
                        </div>
                    </div>
                </div>

                <div class="border-t border-flyto-ink/10 p-6 md:border-t-0">
                    <p class="font-mono text-xs uppercase tracking-[0.3px] text-flyto-muted">Detalle</p>
                    <dl class="mt-3 space-y-2">
                        <div>
                            <dt class="font-mono text-[10px] uppercase tracking-[0.26px] text-flyto-muted">Pasajeros</dt>
                            <dd class="text-sm leading-5"><?= $html($pasajerosLabel) ?></dd>
                        </div>
                        <div>
                            <dt class="font-mono text-[10px] uppercase tracking-[0.26px] text-flyto-muted">Total pagado</dt>
                            <dd class="text-sm leading-5">AR$ <?= $html($formatMoney($reserva->precioTotal)) ?></dd>
                        </div>
                    </dl>
                </div>
            </div>

            <div class="border-t border-flyto-ink/10 px-6 py-4">
                <p class="font-mono text-xs uppercase tracking-[0.3px] text-flyto-muted">Pasajeros</p>
                <ul class="mt-3 grid gap-2 sm:grid-cols-2">
                    <?php foreach ($reserva->pasajeros as $pasajero): ?>
                        <li class="flex items-center gap-2 text-sm leading-5">
                            <svg class="h-3.5 w-3.5 shrink-0 text-flyto-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4.5 21a7.5 7.5 0 0 1 15 0"/></svg>
                            <?= $html(trim($pasajero->nombre . ' ' . $pasajero->apellido)) ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </article>

        <section class="mt-8 border border-flyto-ink/10 bg-[#e8e4da]/40 p-5" aria-labelledby="proximos-pasos-title">
            <h2 id="proximos-pasos-title" class="font-mono text-xs font-medium uppercase tracking-[0.3px]">Próximos pasos</h2>
            <ol class="mt-3 space-y-2.5 text-xs leading-5 text-flyto-muted">
                <li class="flex gap-3"><span class="flex h-5 w-5 shrink-0 items-center justify-center bg-flyto-navy/10 font-mono font-medium text-flyto-navy">1</span><span>Recibirás el itinerario completo y tus tarjetas de embarque por correo electrónico.</span></li>
                <li class="flex gap-3"><span class="flex h-5 w-5 shrink-0 items-center justify-center bg-flyto-navy/10 font-mono font-medium text-flyto-navy">2</span><span>Presentate en el aeropuerto con 2 horas de anticipación para vuelos internacionales.</span></li>
                <li class="flex gap-3"><span class="flex h-5 w-5 shrink-0 items-center justify-center bg-flyto-navy/10 font-mono font-medium text-flyto-navy">3</span><span>Podés gestionar tu reserva desde «Mis reservas» en cualquier momento.</span></li>
            </ol>
        </section>

        <div class="mt-8 grid gap-3 sm:grid-cols-2">
            <a href="<?= $html($basePath) ?>/mi-perfil/mis-reservas" class="flex h-[46px] items-center justify-center gap-2 border border-flyto-ink/10 bg-transparent px-5 text-sm font-medium transition hover:border-flyto-ink/30 hover:bg-white">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M3 11.5 12 4l9 7.5"/><path d="M5.5 10v10h13V10M9 20v-6h6v6"/></svg>
                Ver mis reservas
            </a>
            <a href="<?= $html($basePath) ?>/vuelos/buscar" class="flex h-[46px] items-center justify-center gap-2 bg-flyto-navy px-5 text-sm font-medium text-flyto-sand transition hover:bg-flyto-ink">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M21.5 16.5v2l-8-1.5v4l2 1.5V24L12 23l-3.5 1v-1.5l2-1.5v-4l-8 1.5v-2l8-4V7a1.5 1.5 0 0 1 3 0v5.5l8 4Z" transform="rotate(-45 12 12)"/></svg>
                Buscar otro vuelo
            </a>
        </div>
    </div>
</section>
