<?php

namespace App\Novedades;

use App\Novedades\Controllers\BorrarNovedadActionController;
use App\Novedades\Controllers\CrearNovedadActionController;
use App\Novedades\Controllers\EditarNovedadActionController;

require_once __DIR__ . '/controllers/crear-novedad-action.controller.php';
require_once __DIR__ . '/controllers/editar-novedad-action.controller.php';
require_once __DIR__ . '/controllers/borrar-novedad-action.controller.php';

return [
    'prefix' => '',
    'routes' => [
        ['POST', '/admin/novedades/crear', CrearNovedadActionController::class, 'crear'],
        ['POST', '/admin/novedades/editar', EditarNovedadActionController::class, 'editar'],
        ['POST', '/admin/novedades/borrar', BorrarNovedadActionController::class, 'borrar'],
    ],
];
