<?php

$basePath = $basePath ?? '';
$ciudades = $ciudades ?? [];
$flight = $flight ?? [];
$selectedOrigen = (string) ($flight['origen'] ?? '');
$selectedDestino = (string) ($flight['destino'] ?? '');
$selectedFecha = (string) ($flight['fechaSalida'] ?? '');
$selectedPasajeros = (string) ($flight['cantidadPasajeros'] ?? '');

$cityDescription = static function (array $ciudades, string $selectedId, string $fallback = ''): string {
    foreach ($ciudades as $ciudad) {
        if ((string) $ciudad['id'] === $selectedId) {
            return sprintf('%s, %s', $ciudad['nombre'], $ciudad['nombrePais']);
        }
    }

    return $fallback;
};

$origenDescripcion = (string) ($flight['origen_nombre'] ?? $cityDescription($ciudades, $selectedOrigen));
$destinoDescripcion = (string) ($flight['destino_nombre'] ?? $cityDescription($ciudades, $selectedDestino));

$renderCityOptions = static function (array $ciudades, string $selectedId): void {
    ?>
    <option value="" <?= $selectedId === '' ? 'selected' : '' ?> disabled>Seleccionar</option>
    <?php

    foreach ($ciudades as $ciudad) {
        $id = (string) $ciudad['id'];
        $label = sprintf('%s', $ciudad['nombre']);
        $description = sprintf('%s, %s', $ciudad['nombre'], $ciudad['nombrePais']);
        ?>
        <option
            value="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>"
            data-description="<?= htmlspecialchars($description, ENT_QUOTES, 'UTF-8') ?>"
            <?= $id === $selectedId ? 'selected' : '' ?>
        >
            <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
        </option>
        <?php
    }
};

?>
<form id="buscar-vuelos" action="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>/vuelos/buscar" method="get" class="bg-white p-6 shadow-flyto">
    <div class="grid border border-flyto-ink/10 md:grid-cols-[1fr_56px_1fr_160px_140px]">
        <label class="block border-b border-flyto-ink/10 p-4 md:border-b-0 md:border-r">
            <span class="font-mono text-xs uppercase tracking-[0.3px] text-flyto-muted">Origen</span>
            <select name="origen" class="mt-1 block w-full bg-transparent font-display text-2xl font-semibold leading-6 text-flyto-ink outline-none" aria-describedby="origen_nombre" data-city-select data-description-target="origen_nombre" required>
                <?php $renderCityOptions($ciudades, $selectedOrigen); ?>
            </select>
            <span id="origen_nombre" class="mt-1 block text-xs text-flyto-muted"><?= htmlspecialchars($origenDescripcion, ENT_QUOTES, 'UTF-8') ?></span>
        </label>

        <button id="change-destiny-origin" type="button" class="flex h-12 items-center justify-center border-b border-flyto-ink/10 text-flyto-muted hover:text-flyto-navy md:h-auto md:border-b-0 md:border-r" aria-label="Intercambiar origen y destino">
            <svg class="h-4 w-4 text-flyto-muted" viewBox="0 0 24 24" fill="none">
                <path d="M7 7H19M19 7L16 4M19 7L16 10M17 17H5M5 17L8 14M5 17L8 20" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>

        <label class="block border-b border-flyto-ink/10 p-4 md:border-b-0 md:border-r">
            <span class="font-mono text-xs uppercase tracking-[0.3px] text-flyto-muted">Destino</span>
            <select name="destino" class="mt-1 block w-full bg-transparent font-display text-2xl font-semibold leading-6 text-flyto-ink outline-none" aria-describedby="destino_nombre" data-city-select data-description-target="destino_nombre" required>
                <?php $renderCityOptions($ciudades, $selectedDestino); ?>
            </select>
            <span id="destino_nombre" class="mt-1 block text-xs text-flyto-muted"><?= htmlspecialchars($destinoDescripcion, ENT_QUOTES, 'UTF-8') ?></span>
        </label>

        <label class="block border-b border-flyto-ink/10 p-4 md:border-b-0 md:border-r">
            <span class="font-mono text-xs uppercase tracking-[0.3px] text-flyto-muted">Fecha</span>
            <input
                type="date"
                name="fechaSalida"
                value="<?= htmlspecialchars($selectedFecha, ENT_QUOTES, 'UTF-8') ?>"
                class="mt-1 block h-8 w-full bg-transparent text-sm font-medium leading-5 text-flyto-ink outline-none"
                required
            >
        </label>

        <label class="block p-4">
            <span class="font-mono text-xs uppercase tracking-[0.3px] text-flyto-muted">Pasajeros</span>
            <select name="cantidadPasajeros" class="mt-1 block w-full bg-transparent text-sm font-medium leading-5 text-flyto-ink outline-none" required>
                <option value="" <?= $selectedPasajeros === '' ? 'selected' : '' ?> disabled>Seleccionar</option>
                <?php for ($passengers = 1; $passengers <= 4; $passengers++): ?>
                    <option value="<?= $passengers ?>" <?= (string) $passengers === $selectedPasajeros ? 'selected' : '' ?>>
                        <?= $passengers ?>
                    </option>
                <?php endfor; ?>
            </select>
        </label>
    </div>

    <div class="mt-4 flex justify-end">
        <button class="inline-flex h-11 items-center gap-2 bg-flyto-navy px-8 text-sm font-medium text-flyto-sand" type="submit">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M10.5 18A7.5 7.5 0 1 1 16 15.45L20 19.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            </svg>
            Buscar vuelos
        </button>
    </div>
</form>
