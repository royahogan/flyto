<?php

$news = $news ?? [];
$showNextNewsButton = $showNextNewsButton ?? true;
$fecha = $news['fechaPublicacion'] ?? '';
$texto = $news['texto'] ?? '';

?>
<article class="border border-flyto-ink/10 bg-white p-6 md:border-0 md:bg-transparent md:p-0">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <p class="font-mono text-xs uppercase tracking-[1.2px] text-flyto-muted">
            <?= htmlspecialchars($fecha, ENT_QUOTES, 'UTF-8') ?>
        </p>
        <p class="bg-flyto-mist px-2 py-1 font-mono text-xs text-flyto-ink">
            <?= htmlspecialchars($news['categoria'] ?? '', ENT_QUOTES, 'UTF-8') ?>
        </p>
    </div>
    <h3 class="mt-5 font-display text-[23px] font-medium leading-8 text-flyto-ink">
        <?= htmlspecialchars($news['titulo'] ?? '', ENT_QUOTES, 'UTF-8') ?>
    </h3>
    <p class="mt-4 text-sm leading-[22.75px] text-flyto-muted">
        <?= htmlspecialchars($texto, ENT_QUOTES, 'UTF-8') ?>
    </p>
    <?php if ($showNextNewsButton): ?>
        <button type="button" class="js-next-news mt-6 inline-flex items-center gap-2 text-xs font-medium text-flyto-muted hover:text-flyto-navy">
            Siguiente noticia
            <span aria-hidden="true">&rarr;</span>
        </button>
    <?php endif; ?>
</article>
