<?php

$flash = $flash ?? [];
$oldInput = $oldInput ?? [];
$validationErrors = $validationErrors ?? [];
$contactRedirectTo = $contactRedirectTo ?? 'contacto';

?>
<div class="bg-flyto-navy px-6 pt-16 pb-8 text-flyto-sand">
    <div class="mx-auto max-w-7xl">
        <p class="font-mono text-xs uppercase tracking-[1.2px] text-flyto-gold">Estamos para ayudarte</p>
        <h1 class="mt-4 font-display text-[32px] font-medium leading-10 md:text-[47.8px] md:leading-[59.76px]">Contacto</h1>
    </div>
</div>
<?php require __DIR__ . '/../../../home/views/sections/contact.section.php'; ?>
