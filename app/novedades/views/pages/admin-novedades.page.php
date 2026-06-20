<?php

$novedades = $novedades ?? [];
$flash = $flash ?? [];
$oldInput = $oldInput ?? [];
$validationErrors = $validationErrors ?? [];
$basePath = $basePath ?? '';

$html = fn ($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$dateValue = function ($value): string {
    $value = trim((string) $value);

    if ($value === '') {
        return '';
    }

    return substr(str_replace(' ', 'T', $value), 0, 16);
};
$fieldError = function (string $field, bool $active = true) use ($validationErrors, $html): string {
    if (!$active || empty($validationErrors[$field])) {
        return '';
    }

    return '<p class="mt-1 text-xs text-red-700">' . $html($validationErrors[$field]) . '</p>';
};

$createOldInput = empty($oldInput['id']) ? $oldInput : [];

?>
<div class="bg-flyto-navy px-6 pt-16 pb-8 text-flyto-sand">
    <div class="mx-auto max-w-7xl">
        <p class="font-mono text-xs uppercase tracking-[1.2px] text-flyto-gold">Admin</p>
        <h1 class="mt-4 font-display text-[32px] font-medium leading-10 md:text-[47.8px] md:leading-[59.76px]">Administrar novedades</h1>
        <p class="mt-3 max-w-xl text-sm leading-[22.75px] text-flyto-sand/60">Gestion de publicaciones visibles en Flyto.</p>
    </div>
</div>

<section class="bg-flyto-sand px-6 py-12">
    <div class="mx-auto grid max-w-7xl gap-8">
        <?php if (!empty($flash['success'])): ?>
            <div class="border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                <?= $html($flash['success']) ?>
            </div>
        <?php elseif (!empty($flash['error'])): ?>
            <div class="border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                <?= $html($flash['error']) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($validationErrors['general'])): ?>
            <div class="border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                <?= $html($validationErrors['general']) ?>
            </div>
        <?php endif; ?>

        <section class="border border-flyto-ink/10 bg-white p-6" aria-labelledby="crear-novedad">
            <h2 id="crear-novedad" class="font-display text-[26px] font-medium leading-8 text-flyto-ink">Crear novedad</h2>
            <form class="mt-6 grid gap-4" method="post" action="<?= $html($basePath) ?>/admin/novedades/crear">
                <div>
                    <label class="block text-xs font-medium uppercase tracking-[1.2px] text-flyto-muted" for="crear-titulo">Titulo</label>
                    <input id="crear-titulo" name="titulo" type="text" maxlength="160" value="<?= $html($createOldInput['titulo'] ?? '') ?>" class="mt-2 w-full border border-flyto-ink/15 px-3 py-2 text-sm text-flyto-ink">
                    <?= $fieldError('titulo', $createOldInput !== []) ?>
                </div>
                <div>
                    <label class="block text-xs font-medium uppercase tracking-[1.2px] text-flyto-muted" for="crear-categoria">Categoria</label>
                    <input id="crear-categoria" name="categoria" type="text" maxlength="120" value="<?= $html($createOldInput['categoria'] ?? '') ?>" class="mt-2 w-full border border-flyto-ink/15 px-3 py-2 text-sm text-flyto-ink">
                    <?= $fieldError('categoria', $createOldInput !== []) ?>
                </div>
                <div>
                    <label class="block text-xs font-medium uppercase tracking-[1.2px] text-flyto-muted" for="crear-fecha-expiracion">Fecha de expiracion</label>
                    <input id="crear-fecha-expiracion" name="fechaExpiracion" type="datetime-local" value="<?= $html($dateValue($createOldInput['fechaExpiracion'] ?? '')) ?>" class="mt-2 w-full border border-flyto-ink/15 px-3 py-2 text-sm text-flyto-ink">
                    <?= $fieldError('fechaExpiracion', $createOldInput !== []) ?>
                </div>
                <div>
                    <label class="block text-xs font-medium uppercase tracking-[1.2px] text-flyto-muted" for="crear-texto">Texto</label>
                    <textarea id="crear-texto" name="texto" rows="5" maxlength="2000" class="mt-2 w-full border border-flyto-ink/15 px-3 py-2 text-sm text-flyto-ink"><?= $html($createOldInput['texto'] ?? '') ?></textarea>
                    <?= $fieldError('texto', $createOldInput !== []) ?>
                </div>
                <div>
                    <button type="submit" class="inline-flex items-center justify-center bg-flyto-navy px-5 py-3 text-sm font-medium text-flyto-sand hover:bg-flyto-ink">
                        Crear novedad
                    </button>
                </div>
            </form>
        </section>

        <section class="grid gap-5" aria-labelledby="listado-novedades">
            <div class="flex flex-wrap items-end justify-between gap-3">
                <div>
                    <p class="font-mono text-xs uppercase tracking-[1.2px] text-flyto-muted">Listado</p>
                    <h2 id="listado-novedades" class="mt-2 font-display text-[26px] font-medium leading-8 text-flyto-ink">Novedades cargadas</h2>
                </div>
                <p class="text-sm text-flyto-muted"><?= count($novedades) ?> registros</p>
            </div>

            <?php if ($novedades === []): ?>
                <div class="border border-flyto-ink/10 bg-white p-6 text-sm text-flyto-muted">
                    No hay novedades cargadas.
                </div>
            <?php endif; ?>

            <?php foreach ($novedades as $news): ?>
                <?php
                $newsId = (string) ($news['id'] ?? '');
                $isActiveOldInput = isset($oldInput['id']) && (string) $oldInput['id'] === $newsId;
                $formInput = $isActiveOldInput ? $oldInput : $news;
                ?>
                <article class="border border-flyto-ink/10 bg-white p-6">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <p class="font-mono text-xs uppercase tracking-[1.2px] text-flyto-muted">
                                <?= $html($news['estado'] ?? '') ?> - Publicada <?= $html($news['fechaPublicacion'] ?? '') ?>
                            </p>
                            <h3 class="mt-2 font-display text-[23px] font-medium leading-8 text-flyto-ink">
                                <?= $html($news['titulo'] ?? '') ?>
                            </h3>
                        </div>
                        <span class="bg-flyto-mist px-2 py-1 font-mono text-xs text-flyto-ink">
                            <?= $html($news['categoria'] ?? '') ?>
                        </span>
                    </div>

                    <form class="mt-6 grid gap-4" method="post" action="<?= $html($basePath) ?>/admin/novedades/editar">
                        <input type="hidden" name="id" value="<?= $html($newsId) ?>">
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="block text-xs font-medium uppercase tracking-[1.2px] text-flyto-muted" for="editar-titulo-<?= $html($newsId) ?>">Titulo</label>
                                <input id="editar-titulo-<?= $html($newsId) ?>" name="titulo" type="text" maxlength="160" value="<?= $html($formInput['titulo'] ?? '') ?>" class="mt-2 w-full border border-flyto-ink/15 px-3 py-2 text-sm text-flyto-ink">
                                <?= $fieldError('titulo', $isActiveOldInput) ?>
                            </div>
                            <div>
                                <label class="block text-xs font-medium uppercase tracking-[1.2px] text-flyto-muted" for="editar-categoria-<?= $html($newsId) ?>">Categoria</label>
                                <input id="editar-categoria-<?= $html($newsId) ?>" name="categoria" type="text" maxlength="120" value="<?= $html($formInput['categoria'] ?? '') ?>" class="mt-2 w-full border border-flyto-ink/15 px-3 py-2 text-sm text-flyto-ink">
                                <?= $fieldError('categoria', $isActiveOldInput) ?>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium uppercase tracking-[1.2px] text-flyto-muted" for="editar-fecha-expiracion-<?= $html($newsId) ?>">Fecha de expiracion</label>
                            <input id="editar-fecha-expiracion-<?= $html($newsId) ?>" name="fechaExpiracion" type="datetime-local" value="<?= $html($dateValue($formInput['fechaExpiracion'] ?? '')) ?>" class="mt-2 w-full border border-flyto-ink/15 px-3 py-2 text-sm text-flyto-ink">
                            <?= $fieldError('fechaExpiracion', $isActiveOldInput) ?>
                        </div>
                        <div>
                            <label class="block text-xs font-medium uppercase tracking-[1.2px] text-flyto-muted" for="editar-texto-<?= $html($newsId) ?>">Texto</label>
                            <textarea id="editar-texto-<?= $html($newsId) ?>" name="texto" rows="4" maxlength="2000" class="mt-2 w-full border border-flyto-ink/15 px-3 py-2 text-sm text-flyto-ink"><?= $html($formInput['texto'] ?? '') ?></textarea>
                            <?= $fieldError('texto', $isActiveOldInput) ?>
                        </div>
                        <div class="flex flex-wrap gap-3">
                            <button type="submit" class="inline-flex items-center justify-center bg-flyto-navy px-4 py-2 text-sm font-medium text-flyto-sand hover:bg-flyto-ink">
                                Guardar cambios
                            </button>
                        </div>
                    </form>

                    <form class="mt-3" method="post" action="<?= $html($basePath) ?>/admin/novedades/borrar">
                        <input type="hidden" name="id" value="<?= $html($newsId) ?>">
                        <button type="submit" class="inline-flex items-center justify-center border border-red-700 px-4 py-2 text-sm font-medium text-red-700 hover:bg-red-50">
                            Eliminar novedad
                        </button>
                    </form>
                </article>
            <?php endforeach; ?>
        </section>
    </div>
</section>
