<?php

use App\Reservas\Dtos\DatosPasajerosDto;
use App\Vuelos\Models\Vuelo;

/** @var Vuelo $vuelo */
/** @var DatosPasajerosDto $datosPasajeros */
$basePath = $basePath ?? '';
$flash = is_array($flash ?? null) ? $flash : [];
$validationErrors = is_array($validationErrors ?? null) ? $validationErrors : [];
$oldInput = is_array($oldInput ?? null) ? $oldInput : [];
$oldPago = is_array($oldInput['pago'] ?? null) ? $oldInput['pago'] : [];
$html = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$total = $vuelo->precioConPromocion() * $datosPasajeros->cantidadPasajeros;
$formatMoney = static fn (float $amount): string => number_format($amount, 0, ',', '.');
$labelClass = 'font-mono text-[11px] font-medium uppercase tracking-[0.27px] text-flyto-muted';
$fieldClass = 'mt-1.5 h-[42px] w-full border border-flyto-ink/15 bg-white px-3 font-mono text-sm text-flyto-ink outline-none transition placeholder:text-flyto-muted/50 focus:border-flyto-navy focus:ring-1 focus:ring-flyto-navy';
$fieldError = static function (string $field) use ($validationErrors, $html): string {
    $message = $validationErrors["pago.$field"] ?? null;
    return is_string($message) ? '<p class="mt-1 text-xs text-red-700">' . $html($message) . '</p>' : '';
};
$backQuery = http_build_query([
    'vueloId' => $vuelo->id,
    'cantidadPasajeros' => $datosPasajeros->cantidadPasajeros,
]);
?>

<section class="min-h-[696px] bg-flyto-sand pb-16 text-flyto-ink">
    <div class="border-b border-flyto-ink/10 bg-white">
        <ol class="mx-auto grid max-w-5xl grid-cols-2 gap-y-3 px-6 py-3 sm:grid-cols-4" aria-label="Progreso de la reserva">
            <?php foreach (['Selección', 'Pasajeros', 'Pago', 'Confirmación'] as $index => $step): ?>
                <?php $active = $index === 2; $complete = $index < 2; ?>
                <li class="flex items-center gap-2 text-xs <?= $active ? 'font-medium text-flyto-ink' : 'text-flyto-muted' ?>">
                    <span class="flex h-6 w-6 shrink-0 items-center justify-center font-mono <?= ($active || $complete) ? 'bg-flyto-navy text-flyto-sand' : 'bg-flyto-mist text-flyto-muted' ?>">
                        <?= $complete ? '&#10003;' : $index + 1 ?>
                    </span>
                    <span><?= $html($step) ?></span>
                    <?php if ($index < 3): ?><span class="ml-1 hidden h-px flex-1 <?= $complete ? 'bg-flyto-navy' : 'bg-flyto-ink/15' ?> sm:block"></span><?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ol>
    </div>

    <div class="mx-auto max-w-5xl px-6 pt-8">
        <a href="<?= $html($basePath) ?>/reservas/pasajeros?<?= $html($backQuery) ?>" class="inline-flex items-center gap-2 text-sm font-medium text-flyto-muted hover:text-flyto-ink">
            <span aria-hidden="true">&larr;</span> Volver a datos de pasajeros
        </a>

        <div class="mt-5 grid gap-8 lg:grid-cols-[minmax(0,640px)_304px]">
            <div>
                <h1 class="font-display text-[28px] font-medium leading-tight">Método de pago</h1>

                <?php if (!empty($flash['error'])): ?>
                    <p class="mt-4 border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert"><?= $html($flash['error']) ?></p>
                <?php endif; ?>
                <?php if (!empty($validationErrors['general']) || !empty($validationErrors['pago'])): ?>
                    <p class="mt-3 text-sm text-red-700" role="alert"><?= $html($validationErrors['general'] ?? $validationErrors['pago']) ?></p>
                <?php endif; ?>

                <form action="<?= $html($basePath) ?>/reservas/crear" method="post" class="mt-6 border border-flyto-ink/10 bg-white p-6">
                    <input type="hidden" name="vueloId" value="<?= $vuelo->id ?>">

                    <label class="block">
                        <span class="<?= $labelClass ?>">Número de tarjeta</span>
                        <span class="relative mt-1.5 block">
                            <input class="h-[42px] w-full border border-flyto-ink/15 bg-white py-2.5 pl-3 pr-10 font-mono text-sm text-flyto-ink outline-none transition placeholder:text-flyto-muted/50 focus:border-flyto-navy focus:ring-1 focus:ring-flyto-navy" type="text" name="pago[numeroTarjeta]" inputmode="numeric" autocomplete="cc-number" placeholder="1234 5678 9012 3456" maxlength="23" pattern="[0-9 ]{13,23}" required>
                            <svg class="absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-flyto-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><rect x="2" y="5" width="20" height="14" rx="1"/><path d="M2 10h20"/></svg>
                        </span>
                        <?= $fieldError('numeroTarjeta') ?>
                    </label>

                    <label class="mt-5 block">
                        <span class="<?= $labelClass ?>">Nombre en la tarjeta</span>
                        <input class="<?= $fieldClass ?> uppercase" type="text" name="pago[nombreTitular]" value="<?= $html($oldPago['nombreTitular'] ?? '') ?>" autocomplete="cc-name" placeholder="NOMBRE APELLIDO" maxlength="120" required>
                        <?= $fieldError('nombreTitular') ?>
                    </label>

                    <div class="mt-5 grid gap-4 sm:grid-cols-2">
                        <label>
                            <span class="<?= $labelClass ?>">Vencimiento</span>
                            <input class="<?= $fieldClass ?>" type="text" name="pago[vencimiento]" value="<?= $html($oldPago['vencimiento'] ?? '') ?>" inputmode="numeric" autocomplete="cc-exp" placeholder="MM/AA" maxlength="5" pattern="(0[1-9]|1[0-2])/[0-9]{2}" required>
                            <?= $fieldError('vencimiento') ?>
                        </label>
                        <label>
                            <span class="<?= $labelClass ?>">CVV</span>
                            <input class="<?= $fieldClass ?>" type="password" name="pago[cvv]" inputmode="numeric" autocomplete="cc-csc" placeholder="•••" maxlength="4" pattern="[0-9]{3,4}" required>
                            <?= $fieldError('cvv') ?>
                        </label>
                    </div>

                    <label class="mt-5 flex items-start gap-2.5 text-xs leading-5 text-flyto-muted">
                        <input class="mt-1 h-[13px] w-[13px] shrink-0 accent-flyto-navy" type="checkbox" name="pago[aceptaTerminos]" value="1" <?= ($oldPago['aceptaTerminos'] ?? '') === '1' ? 'checked' : '' ?> required>
                        <span>Acepto los <span class="underline">términos y condiciones</span> y la <span class="underline">política de privacidad</span>.</span>
                    </label>
                    <?= $fieldError('aceptaTerminos') ?>

                    <button type="submit" class="mt-5 flex h-12 w-full items-center justify-center gap-2 bg-flyto-navy px-5 text-sm font-medium text-flyto-sand hover:bg-flyto-ink">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><rect x="5" y="10" width="14" height="10" rx="1"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>
                        Confirmar y pagar · AR$ <?= $html($formatMoney($total)) ?>
                    </button>
                </form>
            </div>

            <aside class="lg:pt-0" aria-label="Resumen de la reserva">
                <div class="border border-flyto-ink/10 bg-white">
                    <div class="border-b border-flyto-ink/10 px-5 py-4">
                        <p class="font-mono text-xs uppercase tracking-[0.3px] text-flyto-muted">Resumen</p>
                        <p class="mt-1 text-sm font-medium"><?= $html($vuelo->ciudadOrigen['abreviacionCiudad']) ?> &rarr; <?= $html($vuelo->ciudadDestino['abreviacionCiudad']) ?></p>
                    </div>
                    <div class="p-5">
                        <div class="flex items-center gap-3">
                            <span class="flex h-8 w-8 items-center justify-center bg-flyto-gold font-mono text-xs font-medium"><?= $html($vuelo->aerolinea['codigoIataAerolinea']) ?></span>
                            <div><p class="text-xs font-medium"><?= $html($vuelo->aerolinea['nombreAerolinea']) ?></p><p class="font-mono text-xs text-flyto-muted"><?= $html($vuelo->codigoVuelo) ?></p></div>
                        </div>
                        <div class="mt-4 grid grid-cols-[auto_1fr_auto] items-center gap-2">
                            <strong class="font-display text-xl font-medium"><?= $html($vuelo->fechaSalida->format('H:i')) ?></strong>
                            <span class="h-px bg-flyto-ink/20"></span>
                            <strong class="font-display text-xl font-medium"><?= $html($vuelo->fechaLlegada->format('H:i')) ?></strong>
                            <span class="font-mono text-xs text-flyto-muted"><?= $html($vuelo->ciudadOrigen['abreviacionCiudad']) ?></span>
                            <span></span>
                            <span class="text-right font-mono text-xs text-flyto-muted"><?= $html($vuelo->ciudadDestino['abreviacionCiudad']) ?></span>
                        </div>
                        <div class="mt-4 flex items-end justify-between border-t border-flyto-ink/10 pt-4">
                            <span class="text-sm font-medium">Total</span>
                            <strong class="font-display text-2xl font-medium text-flyto-navy">AR$ <?= $html($formatMoney($total)) ?></strong>
                        </div>
                    </div>
                </div>
                <p class="mt-3 flex gap-2 text-xs leading-5 text-flyto-muted"><span aria-hidden="true">&#128737;</span> Tu reserva está protegida. Podés cancelarla sin cargo hasta 72 horas antes.</p>

                <div class="mt-4 border border-flyto-ink/10 bg-white p-5">
                    <p class="font-mono text-xs uppercase tracking-[0.3px] text-flyto-muted">Pasajeros</p>
                    <ul class="mt-3 space-y-2">
                        <?php foreach ($datosPasajeros->pasajeros as $pasajero): ?>
                            <li class="flex items-center gap-2 text-sm"><span aria-hidden="true">&#128100;</span><?= $html(trim($pasajero['nombre'] . ' ' . $pasajero['apellido'])) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </aside>
        </div>
    </div>
</section>
