<?php

namespace App\Vuelos;

use App\Vuelos\Controllers\VueloController;

require_once __DIR__ . '/controllers/vuelo.controller.php';

return [
    'prefix' => '/api/vuelos',
    'routes' => [
        ['GET', '/buscar', VueloController::class, 'buscar'],
    ],
];
