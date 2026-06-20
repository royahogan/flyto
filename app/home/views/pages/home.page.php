<?php

$ciudades = $ciudades ?? [];
$novedades = $novedades ?? [];
$flight = $flight ?? [];
$flash = $flash ?? [];
$oldInput = $oldInput ?? [];
$validationErrors = $validationErrors ?? [];
$contactRedirectTo = $contactRedirectTo ?? 'home';

require __DIR__ . '/../sections/hero.section.php';
require __DIR__ . '/../sections/latest-news.section.php';
require __DIR__ . '/../sections/how-to-book.section.php';
require __DIR__ . '/../sections/cta.section.php';
require __DIR__ . '/../sections/contact.section.php';
