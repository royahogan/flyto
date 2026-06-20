<?php

    $steps = [
        ['numero' => '01', 'titulo' => 'Buscá tu vuelo', 'texto' => 'Ingresá origen, destino, fecha y cantidad de pasajeros.'],
        ['numero' => '02', 'titulo' => 'Seleccioná la opción', 'texto' => 'Compará tarifas, condiciones y horarios. Filtrá por precio o aerolínea.'],
        ['numero' => '03', 'titulo' => 'Completá tus datos', 'texto' => 'Ingresá los datos de todos los pasajeros. El proceso es guiado, claro y seguro en cada paso.'],
        ['numero' => '04', 'titulo' => 'Confirmá el pago', 'texto' => 'Pagá con tarjeta. Recibís la confirmación y el itinerario en tu correo de inmediato.'],
    ];

?>
<section id="como-reservar" class="bg-flyto-sand py-16" aria-labelledby="como-reservar-title">
    <div class="mx-auto max-w-7xl px-6">
        <div class="text-center">
            <p class="font-mono text-xs uppercase tracking-[1.2px] text-flyto-muted">Así de fácil</p>
            <h2 id="como-reservar-title" class="mt-4 font-display text-2xl font-medium leading-[33px] text-flyto-ink">
                ¿Cómo realizar una reserva?
            </h2>
        </div>

        <div class="mt-10 grid border border-flyto-ink/10 bg-white md:grid-cols-4">
            <?php foreach ($steps as $step): ?>
                <article class="border-b border-flyto-ink/10 p-6 last:border-b-0 md:border-b-0 md:border-r md:last:border-r-0">
                    <div class="flex items-center justify-between">
                        <p class="font-display text-base text-flyto-muted/50"><?= htmlspecialchars($step['numero'], ENT_QUOTES, 'UTF-8') ?></p>
                        <span class="flex h-6 w-6 items-center justify-center bg-flyto-mist text-flyto-muted" aria-hidden="true">
                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none">
                                <path d="M5 12H19M13 6L19 12L13 18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                    </div>
                    <h3 class="mt-6 font-display text-[16.8px] font-medium leading-[25.2px] text-flyto-ink">
                        <?= htmlspecialchars($step['titulo'], ENT_QUOTES, 'UTF-8') ?>
                    </h3>
                    <p class="mt-3 text-xs leading-[19.5px] text-flyto-muted">
                        <?= htmlspecialchars($step['texto'], ENT_QUOTES, 'UTF-8') ?>
                    </p>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
