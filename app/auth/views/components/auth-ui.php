<?php

if (!function_exists('flytoAuthUrl')) {
    function flytoAuthUrl(string $basePath, string $path): string
    {
        return htmlspecialchars(rtrim($basePath, '/') . $path, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('flytoAuthField')) {
    function flytoAuthField(
        string $label,
        string $name,
        string $type = 'text',
        string $placeholder = '',
        string $autocomplete = '',
        string $extraAttributes = '',
        string $value = '',
        string $error = ''
    ): void {
        ?>
        <label class="block">
            <span class="font-mono text-[10.4px] font-medium uppercase tracking-[0.26px] text-flyto-muted"><?= $label ?></span>
            <input
                required
                name="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>"
                type="<?= htmlspecialchars($type, ENT_QUOTES, 'UTF-8') ?>"
                class="mt-1 h-[41.6px] w-full border border-flyto-ink/10 bg-white px-3 text-sm text-flyto-ink outline-none placeholder:text-flyto-muted/40 focus:border-flyto-navy"
                placeholder="<?= htmlspecialchars($placeholder, ENT_QUOTES, 'UTF-8') ?>"
                <?php if ($type !== 'password' && $value !== ''): ?>value="<?= htmlspecialchars($value, ENT_QUOTES, 'UTF-8') ?>"<?php endif; ?>
                <?php if ($autocomplete !== ''): ?>autocomplete="<?= htmlspecialchars($autocomplete, ENT_QUOTES, 'UTF-8') ?>"<?php endif; ?>
                <?= $extraAttributes ?>
            >
            <?php if ($error !== ''): ?>
                <p class="mt-1 text-xs leading-5 text-flyto-ink"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>
        </label>
        <?php
    }
}

if (!function_exists('flytoPasswordRequirements')) {
    function flytoPasswordRequirements(): void
    {
        $requirements = [
            'M&iacute;nimo 8 caracteres',
            'Al menos una letra may&uacute;scula',
            'Al menos un n&uacute;mero',
            'Al menos un car&aacute;cter especial (!@#$%...)',
            'Las contrase&ntilde;as coinciden',
        ];
        ?>
        <div class="border border-flyto-ink/10 bg-flyto-mist/40 px-4 py-4">
            <p class="font-mono text-xs uppercase tracking-[1.2px] text-flyto-muted">Requisitos de contrase&ntilde;a</p>
            <ul class="mt-3 grid gap-1.5">
                <?php foreach ($requirements as $requirement): ?>
                    <li class="flex items-center gap-3 text-xs leading-4 text-flyto-muted">
                        <span class="h-1.5 w-1.5 rounded-full bg-flyto-muted/30" aria-hidden="true"></span>
                        <span><?= $requirement ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php
    }
}

if (!function_exists('flytoAuthShellStart')) {
    function flytoAuthShellStart(string $eyebrow, string $title, string $maxWidth = 'max-w-md'): void
    {
        ?>
        <section class="bg-flyto-sand px-6 py-16 md:flex md:min-h-[590px] md:items-center md:justify-center">
            <div class="w-full <?= htmlspecialchars($maxWidth, ENT_QUOTES, 'UTF-8') ?>">
                <p class="font-mono text-xs uppercase tracking-[1.2px] text-flyto-muted"><?= $eyebrow ?></p>
                <h1 class="mt-2 font-display text-[28px] font-medium leading-[35px] text-flyto-ink"><?= $title ?></h1>
        <?php
    }
}

if (!function_exists('flytoAuthShellEnd')) {
    function flytoAuthShellEnd(): void
    {
        ?>
            </div>
        </section>
        <?php
    }
}

if (!function_exists('flytoBackIcon')) {
    function flytoBackIcon(): string
    {
        return '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>';
    }
}

if (!function_exists('flytoStatusCard')) {
    function flytoStatusCard(
        string $iconPath,
        string $eyebrow,
        string $title,
        string $body,
        string $actions
    ): void {
        ?>
        <section class="bg-flyto-sand px-6 py-16 md:flex md:min-h-[590px] md:items-center md:justify-center">
            <div class="w-full max-w-md border border-flyto-ink/10 bg-white p-10 text-center">
                <span class="mx-auto flex h-12 w-12 items-center justify-center bg-flyto-navy/10 text-flyto-navy" aria-hidden="true">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                        <?= $iconPath ?>
                    </svg>
                </span>
                <p class="mt-6 font-mono text-xs uppercase tracking-[1.2px] text-flyto-muted"><?= $eyebrow ?></p>
                <h1 class="mt-2 font-display text-[25.6px] font-medium leading-8 text-flyto-ink"><?= $title ?></h1>
                <p class="mx-auto mt-4 max-w-[367px] text-sm leading-[22.75px] text-flyto-muted"><?= $body ?></p>
                <div class="mt-8"><?= $actions ?></div>
            </div>
        </section>
        <?php
    }
}
