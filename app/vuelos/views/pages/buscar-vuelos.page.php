<?php

use App\Vuelos\Dtos\BuscarVuelosDto;

/** @var array $resultadoBusqueda */

$resultadoBusqueda = $resultadoBusqueda ?? [];
/** @var BuscarVuelosDto $criterios */
$criterios = $resultadoBusqueda['criterios'];
$vuelos = $resultadoBusqueda['vuelos'] ?? [];
$aerolineas = $resultadoBusqueda['aerolineas'] ?? [];
$precioMaximoDisponible = (float) ($resultadoBusqueda['precioMaximoDisponible'] ?? 0);
$precioMaximoSeleccionado = (float) ($resultadoBusqueda['precioMaximoSeleccionado'] ?? $precioMaximoDisponible);
$ciudades = $ciudades ?? [];
$basePath = $basePath ?? '';

$ciudadesPorId = [];
foreach ($ciudades as $ciudad) {
    $ciudadesPorId[(int) $ciudad['id']] = $ciudad;
}

$origen = $ciudadesPorId[$criterios->origen] ?? ['nombre' => 'Origen', 'abreviacion' => 'ORI'];
$destino = $ciudadesPorId[$criterios->destino] ?? ['nombre' => 'Destino', 'abreviacion' => 'DST'];
$formatMoney = static fn (float $amount): string => '$' . number_format($amount, 0, ',', '.');
$buildQuery = static function (array $overrides = []) use ($criterios): string {
    $query = [
        'origen' => $criterios->origen,
        'destino' => $criterios->destino,
        'fechaSalida' => $criterios->fechaSalida,
        'cantidadPasajeros' => $criterios->cantidadPasajeros,
        'orden' => $criterios->orden,
    ];

    if ($criterios->precioMaximo !== null) {
        $query['precioMaximo'] = $criterios->precioMaximo;
    }

    if ($criterios->aerolineas !== []) {
        $query['aerolineas'] = implode(',', $criterios->aerolineas);
    }

    foreach ($overrides as $key => $value) {
        if ($value === null) {
            unset($query[$key]);
            continue;
        }

        $query[$key] = $value;
    }

    return http_build_query($query);
};

$renderFilterForm = static function (string $id) use ($basePath, $criterios, $aerolineas, $precioMaximoDisponible, $precioMaximoSeleccionado, $formatMoney): void {
    ?>
    <form id="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>" action="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>/vuelos/buscar" method="get" class="space-y-6">
        <input type="hidden" name="origen" value="<?= htmlspecialchars((string) $criterios->origen, ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="destino" value="<?= htmlspecialchars((string) $criterios->destino, ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="fechaSalida" value="<?= htmlspecialchars($criterios->fechaSalida, ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="cantidadPasajeros" value="<?= htmlspecialchars((string) $criterios->cantidadPasajeros, ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="orden" value="<?= htmlspecialchars($criterios->orden, ENT_QUOTES, 'UTF-8') ?>">

        <fieldset>
            <legend class="font-mono text-xs uppercase tracking-[0.3px] text-flyto-muted">Precio maximo</legend>
            <div class="mt-3">
                <input
                    type="range"
                    name="precioMaximo"
                    min="0"
                    max="<?= htmlspecialchars((string) max(0, (int) ceil($precioMaximoDisponible)), ENT_QUOTES, 'UTF-8') ?>"
                    step="1000"
                    value="<?= htmlspecialchars((string) max(0, (int) ceil($precioMaximoSeleccionado)), ENT_QUOTES, 'UTF-8') ?>"
                    class="w-full accent-flyto-navy"
                    <?= $precioMaximoDisponible <= 0 ? 'disabled' : '' ?>
                >
                <div class="mt-2 flex items-center justify-between text-xs text-flyto-muted">
                    <span>$0</span>
                    <span>Hasta <?= htmlspecialchars($formatMoney($precioMaximoSeleccionado), ENT_QUOTES, 'UTF-8') ?></span>
                </div>
            </div>
        </fieldset>

        <fieldset>
            <legend class="font-mono text-xs uppercase tracking-[0.3px] text-flyto-muted">Aerolineas</legend>
            <div class="mt-3 space-y-3">
                <?php if ($aerolineas === []): ?>
                    <p class="text-sm leading-6 text-flyto-muted">No hay aerolineas para estos criterios.</p>
                <?php endif; ?>
                <?php foreach ($aerolineas as $aerolinea): ?>
                    <?php $codigoIata = (string) $aerolinea['codigoIata']; ?>
                    <label class="flex cursor-pointer items-center gap-3 text-sm text-flyto-ink">
                        <input
                            type="checkbox"
                            name="aerolineas[]"
                            value="<?= htmlspecialchars($codigoIata, ENT_QUOTES, 'UTF-8') ?>"
                            class="h-4 w-4 shrink-0 accent-flyto-navy"
                            <?= in_array($codigoIata, $criterios->aerolineas, true) ? 'checked' : '' ?>
                        >
                        <span><?= htmlspecialchars($aerolinea['nombre'], ENT_QUOTES, 'UTF-8') ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
        </fieldset>

        <div class="flex items-center gap-3">
            <button type="submit" class="inline-flex h-10 flex-1 items-center justify-center bg-flyto-navy px-4 text-sm font-medium text-flyto-sand">
                Filtrar
            </button>
            <a href="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>/vuelos/buscar?<?= htmlspecialchars(http_build_query([
                'origen' => $criterios->origen,
                'destino' => $criterios->destino,
                'fechaSalida' => $criterios->fechaSalida,
                'cantidadPasajeros' => $criterios->cantidadPasajeros,
                'orden' => $criterios->orden,
            ]), ENT_QUOTES, 'UTF-8') ?>" class="inline-flex h-10 items-center justify-center border border-flyto-ink/15 px-4 text-sm font-medium text-flyto-muted">
                Limpiar
            </a>
        </div>
    </form>
    <?php
};

$flight = [
    'origen' => $criterios->origen,
    'origen_nombre' => $origen['nombre'],
    'destino' => $criterios->destino,
    'destino_nombre' => $destino['nombre'],
    'fechaSalida' => $criterios->fechaSalida,
    'cantidadPasajeros' => $criterios->cantidadPasajeros,
];

?>
<section class="bg-flyto-navy py-10 text-flyto-sand md:py-14">
    <div class="mx-auto max-w-7xl px-6">
        <p class="font-mono text-xs uppercase tracking-[1.2px] text-flyto-gold">Busqueda de vuelos</p>
        <div class="mt-4 grid gap-4 md:grid-cols-[1fr_auto] md:items-end">
            <div>
                <h1 class="font-display text-[34px] font-medium leading-tight md:text-[48px]">
                    <?= htmlspecialchars($origen['nombre'], ENT_QUOTES, 'UTF-8') ?> a <?= htmlspecialchars($destino['nombre'], ENT_QUOTES, 'UTF-8') ?>
                </h1>
                <p class="mt-3 text-sm leading-6 text-flyto-sand/70">
                    <?= htmlspecialchars((new DateTime($criterios->fechaSalida))->format('d/m/Y'), ENT_QUOTES, 'UTF-8') ?> · <?= htmlspecialchars((string) $criterios->cantidadPasajeros, ENT_QUOTES, 'UTF-8') ?> pasajero<?= $criterios->cantidadPasajeros === 1 ? '' : 's' ?>
                </p>
            </div>
            <p class="font-mono text-xs uppercase tracking-[0.3px] text-flyto-sand/60">
                <?= htmlspecialchars((string) count($vuelos), ENT_QUOTES, 'UTF-8') ?> resultados
            </p>
        </div>
    </div>
</section>

<section class="mx-auto max-w-7xl px-6 py-8 md:py-10">
    <?php require __DIR__ . '/../components/flight-search-card.php'; ?>

    <div class="mt-8 lg:hidden">
        <details class="border border-flyto-ink/10 bg-white p-4 shadow-flyto">
            <summary class="flex cursor-pointer list-none items-center justify-between text-sm font-semibold text-flyto-ink">
                Filtros
                <span class="font-mono text-xs uppercase text-flyto-muted">Abrir</span>
            </summary>
            <div class="mt-5 border-t border-flyto-ink/10 pt-5">
                <?php $renderFilterForm('filtros-vuelos-mobile'); ?>
            </div>
        </details>
    </div>

    <div class="mt-8 grid gap-8 lg:grid-cols-[280px_1fr]">
        <aside class="hidden lg:block">
            <div class="sticky top-20 border border-flyto-ink/10 bg-white p-5 shadow-flyto">
                <div class="mb-5 flex items-center">
                    <h2 class="text-sm font-semibold text-flyto-ink">Filtros</h2>
                </div>
                <?php $renderFilterForm('filtros-vuelos-desktop'); ?>
            </div>
        </aside>

        <div>
            <div class="mb-4 flex flex-wrap items-center justify-between gap-3 border-b border-flyto-ink/10 pb-4">
                <p class="text-sm text-flyto-muted">
                    Ordenar por
                </p>
                <div class="flex flex-wrap gap-2">
                    <?php foreach (['precio' => 'Precio', 'duracion' => 'Duracion', 'salida' => 'Salida'] as $orden => $label): ?>
                        <a
                            href="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>/vuelos/buscar?<?= htmlspecialchars($buildQuery(['orden' => $orden]), ENT_QUOTES, 'UTF-8') ?>"
                            class="inline-flex h-9 items-center border px-3 text-sm font-medium <?= $criterios->orden === $orden ? 'border-flyto-navy bg-flyto-navy text-flyto-sand' : 'border-flyto-ink/15 bg-white text-flyto-muted hover:text-flyto-ink' ?>"
                        >
                            <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <?php if ($vuelos === []): ?>
                <div class="border border-flyto-ink/10 bg-white p-8 text-center shadow-flyto">
                    <h2 class="font-display text-2xl font-medium text-flyto-ink">No encontramos vuelos disponibles</h2>
                    <p class="mx-auto mt-3 max-w-lg text-sm leading-6 text-flyto-muted">
                        Proba cambiando la fecha, la cantidad de pasajeros o limpiando los filtros aplicados.
                    </p>
                </div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($vuelos as $vuelo): ?>
                        <?php require __DIR__ . '/../components/flight-result-card.php'; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
