<?php

$navItems = [
    ['label' => 'Inicio', 'href' => '/'],
    ['label' => 'Novedades', 'href' => '/novedades'],
    ['label' => 'Ayuda', 'href' => '/faq'],
    ['label' => 'Contacto', 'href' => '/contacto'],
    ['label' => 'Buscar vuelos', 'href' => '/#hero-section'],
];

$basePath = $basePath ?? '';
$currentPath= $currentPath ?? '';
$isAuthenticated = $isAuthenticated ?? false;
$currentUser = $currentUser ?? null;
$currentUser = is_array($currentUser) ? $currentUser : [];
$userName = trim((string) (($currentUser['nombre'] ?? '') . ' ' . ($currentUser['apellido'] ?? '')));

if ($userName === '') {
    $userName = 'Mi perfil';
}

?>
<header class="sticky top-0 z-50 border-b border-flyto-ink/10 bg-white">
    <a class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:bg-white focus:px-4 focus:py-2 focus:text-sm focus:text-flyto-navy" href="#contenido-principal">
        Saltar al contenido principal
    </a>
    <div class="mx-auto flex h-14 max-w-7xl items-center justify-between px-6">
        <a href="<?= htmlspecialchars($basePath ?: '/', ENT_QUOTES, 'UTF-8') ?>" class="flex items-center gap-2" aria-label="Flyto inicio">
            <span class="flex h-7 w-7 items-center justify-center bg-flyto-navy text-flyto-sand" aria-hidden="true">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none">
                    <path d="M4 12L20 4L15 20L11 13L4 12Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                </svg>
            </span>
            <span class="font-display text-[17.6px] font-semibold leading-none tracking-normal text-flyto-navy">Flyto</span>
        </a>

        <nav class="hidden items-center gap-6 md:flex" aria-label="Navegación principal">
            <?php foreach ($navItems as $item): ?>
                <?php $isActive = $currentPath === $item['href']; ?>
                <a
                    href="<?= htmlspecialchars($basePath . $item['href'], ENT_QUOTES, 'UTF-8') ?>"
                    class="border-b pb-0.5 text-sm font-medium <?= $isActive ? 'border-flyto-ink text-flyto-ink' : 'border-transparent text-flyto-muted hover:text-flyto-ink' ?>"
                >
                    <?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?>
                </a>
            <?php endforeach; ?>
        </nav>

        <?php if ($isAuthenticated): ?>
            <div class="hidden items-center gap-3 md:flex">
                <a href="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>/mi-perfil/mis-reservas" class="text-sm font-medium text-flyto-muted hover:text-flyto-ink">
                    <?= htmlspecialchars($userName, ENT_QUOTES, 'UTF-8') ?>
                </a>
            </div>
        <?php else: ?>
            <div class="hidden items-center gap-3 md:flex">
                <a href="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>/auth/login" class="text-sm font-medium text-flyto-muted hover:text-flyto-ink">Ingresar</a>
                <a href="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>/auth/registro" class="bg-flyto-navy px-4 py-2 text-sm font-medium text-flyto-sand">Registrarse</a>
            </div>
        <?php endif; ?>

        <details class="group relative md:hidden">
            <summary class="flex h-8 w-8 cursor-pointer list-none items-center justify-center text-flyto-navy" aria-label="Abrir menú">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M4 7H20M4 12H20M4 17H20" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
            </summary>
            <nav class="absolute right-0 mt-3 w-56 border border-flyto-ink/10 bg-white p-3 shadow-flyto" aria-label="Navegación móvil">
                <?php foreach ($navItems as $item): ?>
                    <a href="<?= htmlspecialchars($basePath . $item['href'], ENT_QUOTES, 'UTF-8') ?>" class="block px-3 py-2 text-sm font-medium text-flyto-muted hover:bg-flyto-sand hover:text-flyto-ink">
                        <?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?>
                    </a>
                <?php endforeach; ?>
                <?php if ($isAuthenticated): ?>
                    <a href="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>/mi-perfil/mis-reservas" class="mt-2 block bg-flyto-navy px-3 py-2 text-sm font-medium text-flyto-sand">
                        <?= htmlspecialchars($userName, ENT_QUOTES, 'UTF-8') ?>
                    </a>
                <?php else: ?>
                    <a href="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>/auth/login" class="mt-2 block px-3 py-2 text-sm font-medium text-flyto-muted hover:bg-flyto-sand hover:text-flyto-ink">Ingresar</a>
                    <a href="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>/auth/registro" class="block bg-flyto-navy px-3 py-2 text-sm font-medium text-flyto-sand">Registrarse</a>
                <?php endif; ?>
            </nav>
        </details>
    </div>
</header>
