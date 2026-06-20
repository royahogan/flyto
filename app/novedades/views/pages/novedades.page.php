<?php

$novedades = $novedades ?? [];
$flash = $flash ?? [];

?>
<div class="bg-flyto-navy px-6 pt-16 pb-8 text-flyto-sand">
    <div class="mx-auto max-w-7xl">
        <p class="font-mono text-xs uppercase tracking-[1.2px] text-flyto-gold">Novedades</p>
        <h1 class="mt-4 font-display text-[32px] font-medium leading-10 md:text-[47.8px] md:leading-[59.76px]">Ultimas novedades de Flyto</h1>
        <p class="mt-3 max-w-xl text-sm leading-[22.75px] text-flyto-sand/60">Noticias, actualizaciones y novedades de la plataforma y el sector aerocomercial.</p>
    </div>
</div>
<section class="bg-flyto-sand px-6 py-16">
    <div class="mx-auto max-w-7xl">
        <?php if (!empty($flash['success'])): ?>
            <div class="border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                <?= htmlspecialchars((string) $flash['success'], ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php elseif (!empty($flash['error'])): ?>
            <div class="border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                <?= htmlspecialchars((string) $flash['error'], ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <div class="mt-10 grid gap-6 bg-white p-6 border border-flyto-ink/10">
            <?php if ($novedades === []): ?>
                <p class="text-sm leading-[22.75px] text-flyto-muted">No hay novedades vigentes.</p>
            <?php endif; ?>
            <?php foreach ($novedades as $news): ?>
                <?php
                $showNextNewsButton = false;
                require __DIR__ . '/../components/news-card.php';
                ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
