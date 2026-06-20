<?php
    $contactFlash = $flash ?? []; 
    $contactOldInput = $oldInput ?? [];
    $contactValidationErrors = $validationErrors ?? [];
    $contactRedirectTo = $contactRedirectTo ?? 'contacto';
    $contactValue = fn (string $key): string => htmlspecialchars((string) ($contactOldInput[$key] ?? ''), ENT_QUOTES, 'UTF-8');
    $contactError = fn (string $key): string => htmlspecialchars((string) ($contactValidationErrors[$key] ?? ''), ENT_QUOTES, 'UTF-8');
    $contactSelected = fn (string $value): string => (($contactOldInput['asunto'] ?? '') === $value) ? ' selected' : '';
?>
<section id="contacto" class="border-t border-flyto-ink/10 bg-flyto-sand py-16" aria-labelledby="contacto-title">
    <div class="mx-auto max-w-7xl px-6">
        <div class="grid border border-flyto-ink/10 md:grid-cols-[452px_1fr]">
            <div class="bg-white p-8 md:border-r md:border-flyto-ink/10">
                <p class="font-mono text-xs uppercase tracking-[1.2px] text-flyto-muted">Contacto</p>
                <h2 id="contacto-title" class="mt-4 font-display text-2xl font-medium leading-[33px] text-flyto-ink">Póngase en contacto</h2>
                <p class="mt-5 text-sm leading-[22.75px] text-flyto-muted">
                    Nuestro equipo de atención al pasajero está disponible de lunes a viernes de 9 a 20 h, y sábados de 10 a 16 h. También podés escribirnos en cualquier momento por correo.
                </p>

                <div class="mt-8 grid gap-5">
                    <div class="flex gap-4">
                        <span class="flex h-8 w-8 items-center justify-center bg-flyto-navy/10 text-flyto-navy" aria-hidden="true">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none">
                                <path d="M5 4H9L11 9L8.5 10.5C9.6 12.8 11.2 14.4 13.5 15.5L15 13L20 15V19C20 20.1 19.1 21 18 21C9.7 21 3 14.3 3 6C3 4.9 3.9 4 5 4Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <div>
                            <p class="font-mono text-[10.4px] uppercase tracking-[0.26px] text-flyto-muted">Teléfono</p>
                            <a href="tel:+541145678900" class="text-sm leading-5 text-flyto-ink">+54 (11) 4567-8900</a>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <span class="flex h-8 w-8 items-center justify-center bg-flyto-navy/10 text-flyto-navy" aria-hidden="true">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none">
                                <path d="M4 6H20V18H4V6Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                                <path d="M4 7L12 13L20 7" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <div>
                            <p class="font-mono text-[10.4px] uppercase tracking-[0.26px] text-flyto-muted">Correo electrónico</p>
                            <a href="mailto:contacto@flyto.com.ar" class="text-sm leading-5 text-flyto-ink">contacto@flyto.com.ar</a>
                        </div>
                    </div>
                </div>
            </div>

            <form action="<?= htmlspecialchars($basePath ?? '', ENT_QUOTES, 'UTF-8') ?>/contacto/enviar" method="post" class="p-8">
                <input type="hidden" name="redirectTo" value="<?= $contactRedirectTo ?>">
                <p class="font-mono text-xs uppercase tracking-[1.2px] text-flyto-muted">Formulario de consulta</p>
                <?php if (!empty($contactFlash['success'])): ?>
                    <p class="mt-4 border border-flyto-navy bg-flyto-navy/10 px-4 py-3 text-sm leading-5 text-flyto-navy">
                        <?= htmlspecialchars((string) $contactFlash['success'], ENT_QUOTES, 'UTF-8') ?>
                    </p>
                <?php elseif (!empty($contactFlash['error'])): ?>
                    <p class="mt-4 border border-flyto-ink bg-white px-4 py-3 text-sm leading-5 text-flyto-ink">
                        <?= htmlspecialchars((string) $contactFlash['error'], ENT_QUOTES, 'UTF-8') ?>
                    </p>
                <?php endif; ?>
                <div class="mt-6 grid gap-4 md:grid-cols-2">
                    <label class="block">
                        <span class="font-mono text-[10.4px] font-medium uppercase tracking-[0.26px] text-flyto-muted">Nombre</span>
                        <input required name="nombre" value="<?= $contactValue('nombre') ?>" class="mt-1 h-10 w-full border border-flyto-ink/10 bg-white px-3 text-sm outline-none focus:border-flyto-navy" placeholder="Tu nombre">
                        <?php if ($contactError('nombre') !== ''): ?>
                            <p class="mt-1 text-xs leading-5 text-flyto-ink"><?= $contactError('nombre') ?></p>
                        <?php endif; ?>
                    </label>
                    <label class="block">
                        <span class="font-mono text-[10.4px] font-medium uppercase tracking-[0.26px] text-flyto-muted">Apellido</span>
                        <input required name="apellido" value="<?= $contactValue('apellido') ?>" class="mt-1 h-10 w-full border border-flyto-ink/10 bg-white px-3 text-sm outline-none focus:border-flyto-navy" placeholder="Tu apellido">
                        <?php if ($contactError('apellido') !== ''): ?>
                            <p class="mt-1 text-xs leading-5 text-flyto-ink"><?= $contactError('apellido') ?></p>
                        <?php endif; ?>
                    </label>
                </div>
                <label class="mt-4 block">
                    <span class="font-mono text-[10.4px] font-medium uppercase tracking-[0.26px] text-flyto-muted">Correo electrónico</span>
                    <input required name="email" type="email" value="<?= $contactValue('email') ?>" class="mt-1 h-10 w-full border border-flyto-ink/10 bg-white px-3 text-sm outline-none focus:border-flyto-navy" placeholder="nombre@ejemplo.com">
                    <?php if ($contactError('email') !== ''): ?>
                        <p class="mt-1 text-xs leading-5 text-flyto-ink"><?= $contactError('email') ?></p>
                    <?php endif; ?>
                </label>
                <label class="mt-4 block">
                    <span class="font-mono text-[10.4px] font-medium uppercase tracking-[0.26px] text-flyto-muted">Asunto</span>
                    <select required name="asunto" class="mt-1 h-10 w-full border border-flyto-ink/10 bg-white px-3 text-sm outline-none focus:border-flyto-navy">
                        <option<?= $contactSelected('Consulta sobre vuelos') ?>>Consulta sobre vuelos</option>
                        <option<?= $contactSelected('Reserva existente') ?>>Reserva existente</option>
                        <option<?= $contactSelected('Soporte de cuenta') ?>>Soporte de cuenta</option>
                        <option<?= $contactSelected('Otros') ?>>Otros</option>
                    </select>
                    <?php if ($contactError('asunto') !== ''): ?>
                        <p class="mt-1 text-xs leading-5 text-flyto-ink"><?= $contactError('asunto') ?></p>
                    <?php endif; ?>
                </label>
                <label class="mt-4 block">
                    <span class="font-mono text-[10.4px] font-medium uppercase tracking-[0.26px] text-flyto-muted">Mensaje</span>
                    <textarea required name="mensaje" rows="5" class="mt-1 w-full border border-flyto-ink/10 bg-white px-3 py-2 text-sm outline-none focus:border-flyto-navy" placeholder="Describí tu consulta con el mayor detalle posible..."><?= $contactValue('mensaje') ?></textarea>
                    <?php if ($contactError('mensaje') !== ''): ?>
                        <p class="mt-1 text-xs leading-5 text-flyto-ink"><?= $contactError('mensaje') ?></p>
                    <?php endif; ?>
                </label>
                <button class="mt-6 inline-flex h-11 items-center gap-2 bg-flyto-navy px-7 text-sm font-medium text-flyto-sand" type="submit">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M4 12L20 4L15 20L11 13L4 12Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                    </svg>
                    Enviar consulta
                </button>
            </form>
        </div>
    </div>
</section>
