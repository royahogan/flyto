<?php

use App\Vuelos\Models\Vuelo;

/** @var Vuelo $vuelo */
$basePath = $basePath ?? '';
$cantidadPasajeros = (int) ($cantidadPasajeros ?? 1);
$flash = $flash ?? null;
$flash = is_array($flash) ? $flash : [];
$validationErrors = $validationErrors ?? null;
$validationErrors = is_array($validationErrors) ? $validationErrors : [];
$oldInput = $oldInput ?? null;
$oldInput = is_array($oldInput) ? $oldInput : [];
$oldPasajeros = is_array($oldInput['pasajeros'] ?? null) ? array_values($oldInput['pasajeros']) : [];
$html = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$formatMoney = static fn (float $amount): string => '$' . number_format($amount, 0, ',', '.');
$nacionalidades = ['Argentina', 'Brasil', 'Chile', 'Colombia', 'Espana', 'Estados Unidos', 'Mexico', 'Paraguay', 'Peru', 'Uruguay', 'Otra'];
$fieldError = static function (int $index, string $field) use ($validationErrors, $html): string {
    $message = $validationErrors["pasajeros.$index.$field"] ?? null;
    return is_string($message) ? '<p class="mt-1 text-xs text-red-700">' . $html($message) . '</p>' : '';
};
$fieldClass = 'mt-1.5 h-[42px] w-full border border-flyto-ink/15 bg-white px-3 text-sm text-flyto-ink outline-none transition focus:border-flyto-navy focus:ring-1 focus:ring-flyto-navy';
$labelClass = 'font-mono text-[11px] font-medium uppercase tracking-[0.27px] text-flyto-muted';
$backQuery = http_build_query([
    'origen' => $vuelo->ciudadOrigen['idCiudad'],
    'destino' => $vuelo->ciudadDestino['idCiudad'],
    'fechaSalida' => $vuelo->fechaSalida->format('Y-m-d'),
    'cantidadPasajeros' => $cantidadPasajeros,
]);
$total = $vuelo->precioConPromocion() * $cantidadPasajeros;
?>

<section class="min-h-[720px] bg-flyto-sand pb-16 text-flyto-ink">
    <div class="border-b border-flyto-ink/10 bg-white">
        <ol class="mx-auto grid max-w-5xl grid-cols-2 gap-y-3 px-6 py-3 sm:grid-cols-4" aria-label="Progreso de la reserva">
            <?php foreach (['Selección', 'Pasajeros', 'Pago', 'Confirmación'] as $index => $step): ?>
                <?php $active = $index === 1; $complete = $index === 0; ?>
                <li class="flex items-center gap-2 text-xs <?= $active ? 'font-medium text-flyto-ink' : 'text-flyto-muted' ?>">
                    <span class="flex h-6 w-6 shrink-0 items-center justify-center font-mono <?= ($active || $complete) ? 'bg-flyto-navy text-flyto-sand' : 'bg-flyto-mist text-flyto-muted' ?>">
                        <?= $complete ? '&#10003;' : $index + 1 ?>
                    </span>
                    <span><?= $html($step) ?></span>
                    <?php if ($index < 3): ?><span class="ml-1 hidden h-px flex-1 bg-flyto-ink/15 sm:block"></span><?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ol>
    </div>

    <div class="mx-auto max-w-5xl px-6 pt-8">
        <a href="<?= $html($basePath) ?>/vuelos/buscar?<?= $html($backQuery) ?>" class="inline-flex items-center gap-2 text-sm font-medium text-flyto-muted hover:text-flyto-ink">
            <span aria-hidden="true">&larr;</span> Volver a resultados
        </a>

        <div class="mt-5 grid gap-8 lg:grid-cols-[minmax(0,640px)_304px]">
            <div>
                <h1 class="font-display text-[28px] font-medium leading-tight">Datos de los pasajeros</h1>

                <?php if (!empty($flash['error'])): ?>
                    <p class="mt-4 border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert"><?= $html($flash['error']) ?></p>
                <?php endif; ?>
                <?php if (!empty($validationErrors['general']) || !empty($validationErrors['pasajeros'])): ?>
                    <p class="mt-3 text-sm text-red-700" role="alert"><?= $html($validationErrors['general'] ?? $validationErrors['pasajeros']) ?></p>
                <?php endif; ?>

                <form action="<?= $html($basePath) ?>/reservas/pasajeros" method="post" class="mt-6 space-y-5">
                    <input type="hidden" name="vueloId" value="<?= $vuelo->id ?>">
                    <input type="hidden" name="cantidadPasajeros" value="<?= $cantidadPasajeros ?>">

                    <?php for ($index = 0; $index < $cantidadPasajeros; $index++): ?>
                        <?php $old = is_array($oldPasajeros[$index] ?? null) ? $oldPasajeros[$index] : []; ?>
                        <fieldset class="border border-flyto-ink/10 bg-white">
                            <legend class="sr-only">Pasajero <?= $index + 1 ?></legend>
                            <div class="flex items-center gap-3 border-b border-flyto-ink/10 px-6 py-4">
                                <span class="flex h-7 w-7 items-center justify-center bg-flyto-navy font-mono text-xs text-flyto-sand"><?= $index + 1 ?></span>
                                <h2 class="text-sm font-medium">Pasajero <?= $index + 1 ?><?php if ($index === 0): ?> <span class="font-normal text-flyto-muted">(titular)</span><?php endif; ?></h2>
                            </div>
                            <div class="grid gap-x-4 gap-y-4 p-6 sm:grid-cols-2">
                                <label><span class="<?= $labelClass ?>">Nombre</span><input class="<?= $fieldClass ?>" name="pasajeros[<?= $index ?>][nombre]" value="<?= $html($old['nombre'] ?? '') ?>" placeholder="Ej: María" maxlength="80" required><?= $fieldError($index, 'nombre') ?></label>
                                <label><span class="<?= $labelClass ?>">Apellido</span><input class="<?= $fieldClass ?>" name="pasajeros[<?= $index ?>][apellido]" value="<?= $html($old['apellido'] ?? '') ?>" placeholder="Ej: González" maxlength="80" required><?= $fieldError($index, 'apellido') ?></label>
                                <label><span class="<?= $labelClass ?>">DNI / Documento</span><input class="<?= $fieldClass ?>" name="pasajeros[<?= $index ?>][documento]" value="<?= $html($old['documento'] ?? '') ?>" placeholder="12.345.678" maxlength="30" required><?= $fieldError($index, 'documento') ?></label>
                                <label><span class="<?= $labelClass ?>">Pasaporte</span><input class="<?= $fieldClass ?>" name="pasajeros[<?= $index ?>][pasaporte]" value="<?= $html($old['pasaporte'] ?? '') ?>" placeholder="AAB123456" maxlength="30" required><?= $fieldError($index, 'pasaporte') ?></label>
                                <label><span class="<?= $labelClass ?>">Fecha de nacimiento</span><input class="<?= $fieldClass ?>" type="date" name="pasajeros[<?= $index ?>][fechaNacimiento]" value="<?= $html($old['fechaNacimiento'] ?? '') ?>" max="<?= date('Y-m-d', strtotime('-1 day')) ?>" required><?= $fieldError($index, 'fechaNacimiento') ?></label>
                                <label><span class="<?= $labelClass ?>">Nacionalidad</span><select class="<?= $fieldClass ?>" name="pasajeros[<?= $index ?>][nacionalidad]" required><option value="">Seleccionar</option><?php foreach ($nacionalidades as $nacionalidad): ?><option value="<?= $html($nacionalidad) ?>" <?= ($old['nacionalidad'] ?? '') === $nacionalidad ? 'selected' : '' ?>><?= $html($nacionalidad) ?></option><?php endforeach; ?></select><?= $fieldError($index, 'nacionalidad') ?></label>
                                <label class="sm:col-span-2"><span class="<?= $labelClass ?>">Correo electrónico</span><input class="<?= $fieldClass ?>" type="email" name="pasajeros[<?= $index ?>][correoElectronico]" value="<?= $html($old['correoElectronico'] ?? '') ?>" placeholder="nombre@ejemplo.com" maxlength="120" required><?= $fieldError($index, 'correoElectronico') ?></label>
                                <label><span class="<?= $labelClass ?>">Teléfono de contacto</span><input class="<?= $fieldClass ?>" type="tel" name="pasajeros[<?= $index ?>][telefonoContacto]" value="<?= $html($old['telefonoContacto'] ?? '') ?>" placeholder="+54 9 11 1234-5678" maxlength="30" required><?= $fieldError($index, 'telefonoContacto') ?></label>
                            </div>
                        </fieldset>
                    <?php endfor; ?>

                    <button type="submit" class="flex h-12 w-full items-center justify-center gap-2 bg-flyto-navy px-5 text-sm font-medium text-flyto-sand hover:bg-flyto-ink">
                        Continuar al pago <span aria-hidden="true">&rarr;</span>
                    </button>
                </form>
            </div>

            <aside class="lg:pt-[62px]" aria-label="Resumen del vuelo">
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
                        <div class="mt-5 grid grid-cols-[auto_1fr_auto] items-center gap-2">
                            <strong class="font-display text-xl font-medium"><?= $html($vuelo->fechaSalida->format('H:i')) ?></strong>
                            <span class="h-px bg-flyto-ink/20"></span>
                            <strong class="font-display text-xl font-medium"><?= $html($vuelo->fechaLlegada->format('H:i')) ?></strong>
                            <span class="font-mono text-xs text-flyto-muted"><?= $html($vuelo->ciudadOrigen['abreviacionCiudad']) ?></span>
                            <span class="text-center text-xs text-flyto-muted"><?= $html($vuelo->duracionTexto()) ?> · Directo</span>
                            <span class="text-right font-mono text-xs text-flyto-muted"><?= $html($vuelo->ciudadDestino['abreviacionCiudad']) ?></span>
                        </div>
                        <div class="mt-5 border-t border-flyto-ink/10 pt-4 text-xs text-flyto-muted">
                            <div class="flex justify-between"><span>Precio (<?= $cantidadPasajeros ?> pas.)</span><span><?= $html($formatMoney($vuelo->precioConPromocion())) ?> c/u</span></div>
                            <div class="mt-3 flex items-end justify-between border-t border-flyto-ink/10 pt-3 text-flyto-ink"><span class="text-sm font-medium">Total</span><strong class="font-display text-2xl font-medium text-flyto-navy"><?= $html($formatMoney($total)) ?></strong></div>
                        </div>
                    </div>
                </div>
                <p class="mt-3 flex gap-2 text-xs leading-5 text-flyto-muted"><span aria-hidden="true">&#128737;</span> Tu reserva estará protegida hasta completar el proceso.</p>
            </aside>
        </div>
    </div>
</section>
