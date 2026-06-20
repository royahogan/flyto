<?php

namespace App\Reservas;

use App\Reservas\Controllers\CancelarReservaActionController;
use App\Reservas\Controllers\CrearReservaActionController;
use App\Reservas\Controllers\GuardarPasajerosActionController;

require_once __DIR__ . '/controllers/cancelar-reserva-action.controller.php';
require_once __DIR__ . '/controllers/crear-reserva-action.controller.php';
require_once __DIR__ . '/controllers/guardar-pasajeros-action.controller.php';

return [
    'prefix' => '/reservas',
    'routes' => [
        ['POST', '/crear', CrearReservaActionController::class, 'crear'],
        ['POST', '/pasajeros', GuardarPasajerosActionController::class, 'guardar'],
        ['POST', '/cancelar', CancelarReservaActionController::class, 'cancelar'],
    ],
];
