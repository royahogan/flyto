<?php $basePath = $basePath ?? ''; ?>

<footer class="bg-flyto-navy text-flyto-sand">
    <div class="mx-auto grid max-w-7xl gap-10 px-6 py-12 md:grid-cols-[259px_1fr]">
        <div>
            <a href="<?= htmlspecialchars($basePath ?: '/', ENT_QUOTES, 'UTF-8') ?>" class="flex items-center gap-2" aria-label="Flyto inicio">
                <span class="flex h-6 w-6 items-center justify-center bg-flyto-sand/20" aria-hidden="true">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none">
                        <path d="M4 12L20 4L15 20L11 13L4 12Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                    </svg>
                </span>
                <span class="font-display text-base font-medium">Flyto</span>
            </a>
            <p class="mt-4 max-w-[259px] text-xs leading-[19.5px] text-flyto-sand/50">
                Plataforma de reservas de vuelos para viajeros que valoran la claridad y el profesionalismo.
            </p>
        </div>

        <nav class="grid gap-3 text-xs md:justify-start" aria-label="Enlaces importantes">
            <p class="font-mono uppercase tracking-[1.2px] text-flyto-sand/70">Enlaces importantes</p>
            <a href="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>/novedades" class="text-flyto-sand/50 hover:text-flyto-sand">Novedades</a>
            <a href="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>/contacto" class="text-flyto-sand/50 hover:text-flyto-sand">Contacto</a>
            <a href="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>/faq" class="text-flyto-sand/50 hover:text-flyto-sand">FAQ</a>
            <a href="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>/vuelos/buscar" class="text-flyto-sand/50 hover:text-flyto-sand">Buscar Vuelos</a>
            <a href="<?= htmlspecialchars($basePath ?: '/', ENT_QUOTES, 'UTF-8') ?>" class="text-flyto-sand/50 hover:text-flyto-sand">Inicio</a>
        </nav>
    </div>
    <div class="border-t border-flyto-sand/10">
        <div class="mx-auto max-w-7xl px-6 py-4">
            <p class="font-mono text-xs leading-4 text-flyto-sand/40">© 2026 Flyto. Todos los derechos reservados.</p>
        </div>
    </div>
</footer>
