<?php

use App\Vuelos\Models\Vuelo;

/** @var Vuelo $vuelo */
$formatMoney = $formatMoney ?? static fn (float $amount): string => '$' . number_format($amount, 0, ',', '.');
$asientosLibres = $vuelo->asientosLibres();
$asientosTexto = $asientosLibres === 1 ? 'asiento' : 'asientos';
$tienePromocion = $vuelo->promocion !== null;
$precioFinal = $vuelo->precioConPromocion();
$cantidadPasajerosSeleccionada = isset($criterios) ? (int) $criterios->cantidadPasajeros : 1;
$seleccionarQuery = http_build_query([
    'vueloId' => $vuelo->id,
    'cantidadPasajeros' => $cantidadPasajerosSeleccionada,
]);

?>
<article class="border border-flyto-ink/10 bg-white p-5 shadow-flyto md:p-6">
    <div class="grid gap-5 lg:grid-cols-[1fr_auto] lg:items-center">
        <div class="min-w-0">
            <div class="flex items-start gap-4">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center bg-flyto-gold font-mono text-sm font-medium text-flyto-ink">
                    <?= htmlspecialchars($vuelo->aerolinea['codigoIataAerolinea'], ENT_QUOTES, 'UTF-8') ?>
                </div>
                <div class="min-w-0">
                    <h2 class="text-base font-semibold leading-6 text-flyto-ink">
                        <?= htmlspecialchars($vuelo->aerolinea['nombreAerolinea'], ENT_QUOTES, 'UTF-8') ?>
                    </h2>
                    <p class="mt-1 font-mono text-xs uppercase tracking-[0.3px] text-flyto-muted">
                        Vuelo <?= htmlspecialchars($vuelo->codigoVuelo, ENT_QUOTES, 'UTF-8') ?>
                    </p>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-[1fr_auto_1fr] items-center gap-4">
                <div>
                    <p class="font-display text-[32px] font-semibold leading-none text-flyto-ink">
                        <?= htmlspecialchars($vuelo->fechaSalida->format('H:i'), ENT_QUOTES, 'UTF-8') ?>
                    </p>
                    <p class="mt-2 font-mono text-xs uppercase tracking-[0.3px] text-flyto-muted">
                        <?= htmlspecialchars($vuelo->ciudadOrigen['abreviacionCiudad'], ENT_QUOTES, 'UTF-8') ?>
                    </p>
                    <p class="mt-1 text-xs text-flyto-muted">
                        <?= htmlspecialchars($vuelo->ciudadOrigen['nombreCiudad'], ENT_QUOTES, 'UTF-8') ?>
                    </p>
                </div>

                <div class="flex min-w-[96px] flex-col items-center text-center">
                    <p class="font-mono text-xs uppercase tracking-[0.3px] text-flyto-muted">
                        <?= htmlspecialchars($vuelo->duracionTexto(), ENT_QUOTES, 'UTF-8') ?>
                    </p>
                    <div class="my-2 h-px w-full bg-flyto-ink/20"></div>
                    <p class="text-xs text-flyto-muted">Directo</p>
                </div>

                <div class="text-right">
                    <p class="font-display text-[32px] font-semibold leading-none text-flyto-ink">
                        <?= htmlspecialchars($vuelo->fechaLlegada->format('H:i'), ENT_QUOTES, 'UTF-8') ?>
                    </p>
                    <p class="mt-2 font-mono text-xs uppercase tracking-[0.3px] text-flyto-muted">
                        <?= htmlspecialchars($vuelo->ciudadDestino['abreviacionCiudad'], ENT_QUOTES, 'UTF-8') ?>
                    </p>
                    <p class="mt-1 text-xs text-flyto-muted">
                        <?= htmlspecialchars($vuelo->ciudadDestino['nombreCiudad'], ENT_QUOTES, 'UTF-8') ?>
                    </p>
                </div>
            </div>
        </div>

        <div class="border-t border-flyto-ink/10 pt-5 lg:min-w-[190px] lg:border-l lg:border-t-0 lg:pl-6 lg:pt-0">
            <?php if ($tienePromocion): ?>
                <p class="mt-2 text-sm text-flyto-muted line-through">
                    <?= htmlspecialchars($formatMoney($vuelo->precio), ENT_QUOTES, 'UTF-8') ?>
                </p>
            <?php endif; ?>
            <p class="<?= $tienePromocion ? 'mt-2' : '' ?> font-display text-[30px] font-semibold leading-none text-flyto-navy">
                <span class="mb-1 block font-mono text-xs font-normal uppercase tracking-[0.3px] text-flyto-muted">Precio final</span>
                <?= htmlspecialchars($formatMoney($precioFinal), ENT_QUOTES, 'UTF-8') ?>
            </p>
            <p class="mt-2 text-xs text-flyto-muted">por pasajero</p>

            <?php if ($tienePromocion): ?>
                <p class="mt-3 border border-flyto-gold/60 bg-flyto-gold/10 px-3 py-2 text-xs font-semibold leading-5 text-flyto-ink">
                    Promocion aplicada: <?= htmlspecialchars(number_format((float) $vuelo->promocion['descuento'] * 100, 0, ',', '.'), ENT_QUOTES, 'UTF-8') ?>% de descuento
                </p>
            <?php endif; ?>

            <?php if ($asientosLibres < 10): ?>
                <p class="mt-4 border border-flyto-gold/60 bg-flyto-gold/10 px-3 py-2 text-xs font-medium leading-5 text-flyto-ink">
                    Quedan <?= htmlspecialchars((string) $asientosLibres, ENT_QUOTES, 'UTF-8') ?> <?= htmlspecialchars($asientosTexto, ENT_QUOTES, 'UTF-8') ?>
                </p>
            <?php else: ?>
                <p class="mt-4 text-xs text-flyto-muted">
                    <?= htmlspecialchars((string) $asientosLibres, ENT_QUOTES, 'UTF-8') ?> <?= htmlspecialchars($asientosTexto, ENT_QUOTES, 'UTF-8') ?> disponibles
                </p>
            <?php endif; ?>

            <a href="<?= htmlspecialchars(($basePath ?? '') . '/reservas/pasajeros?' . $seleccionarQuery, ENT_QUOTES, 'UTF-8') ?>" class="mt-5 inline-flex h-10 w-full items-center justify-center bg-flyto-navy px-4 text-sm font-medium text-flyto-sand">
                Seleccionar
            </a>
        </div>
    </div>
</article>
