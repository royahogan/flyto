<?php
    $novedades = $novedades ?? [];
    $showNextNewsButton = true;
?>
<section class="mt-16 border-t border-flyto-ink/10 bg-white py-12 md:mt-28" aria-labelledby="ultimas-novedades">
    <div class="mx-auto max-w-7xl px-6">
        <div class="grid border border-flyto-ink/10 md:grid-cols-[282px_1fr]">
            <div class="border-b border-flyto-ink/10 p-6 md:border-b-0 md:border-r md:p-8">
                <p id="ultimas-novedades" class="font-mono text-xs uppercase tracking-[1.2px] text-flyto-muted">Últimas Novedades</p>
                <div class="mt-4 h-px w-8 bg-flyto-gold"></div>
                <p class="mt-6 text-xs leading-[19.5px] text-flyto-muted">
                    Noticias, actualizaciones y novedades de la plataforma y el sector aerocomercial.
                </p>
                <a href="<?= htmlspecialchars($basePath ?? '', ENT_QUOTES, 'UTF-8') ?>/novedades" class="mt-10 inline-flex items-center gap-2 border border-flyto-navy px-4 py-2 text-xs font-medium text-flyto-navy">
                    Ver todas las novedades
                    <span aria-hidden="true">→</span>
                </a>
            </div>
            <div class="p-6 md:p-8" data-news-carousel>
                <?php if ($novedades === []): ?>
                    <p class="text-sm leading-[22.75px] text-flyto-muted">No hay novedades vigentes.</p>
                <?php endif; ?>
                <?php foreach ($novedades as $index => $news): ?>
                    <div data-news-slide class="<?= $index === 0 ? '' : 'hidden' ?>">
                        <?php
                            require __DIR__ . '/../../../novedades/views/components/news-card.php';
                        ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
