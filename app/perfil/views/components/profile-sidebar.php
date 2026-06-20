<?php

$basePath = $basePath ?? '';
$logoutUrl = rtrim($basePath, '/') . '/auth/logout';
$profileSection = $profileSection ?? 'datos';

?>
<aside aria-label="Opciones de usuario">
    <div class="border border-flyto-ink/10 bg-white">
        <a href="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>/mi-perfil/mis-reservas" class="flex h-12 items-center gap-3 border-b border-flyto-ink/10 px-5 text-sm font-medium <?= $profileSection === 'reservas' ? 'bg-flyto-navy/10 text-flyto-navy' : 'text-flyto-muted hover:bg-flyto-sand' ?>">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M6 5H18V20L12 16.5L6 20V5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
            </svg>
            Mis reservas
        </a>
        <a href="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>/mi-perfil/datos" class="flex h-12 w-full items-center gap-3 border-b border-flyto-ink/10 px-5 text-sm font-medium <?= $profileSection === 'datos' ? 'bg-flyto-navy/10 text-flyto-navy' : 'text-flyto-muted hover:bg-flyto-sand' ?>">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M12 12C14.2 12 16 10.2 16 8C16 5.8 14.2 4 12 4C9.8 4 8 5.8 8 8C8 10.2 9.8 12 12 12Z" stroke="currentColor" stroke-width="1.8"/>
                <path d="M5 20C5.8 16.8 8.2 15.2 12 15.2C15.8 15.2 18.2 16.8 19 20" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            </svg>
            Datos personales
        </a>
        <form action="<?= htmlspecialchars($logoutUrl, ENT_QUOTES, 'UTF-8') ?>" method="post">
            <button type="submit" class="flex h-12 w-full items-center gap-3 px-5 text-sm font-medium hover:bg-flyto-sand" style="color: rgba(185, 28, 28, 0.7);">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M10 6H6V18H10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M13 12H21M18 9L21 12L18 15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Cerrar sesi&oacute;n
            </button>
        </form>
    </div>
</aside>
