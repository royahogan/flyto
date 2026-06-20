<?php

namespace App\Contacto;

use App\Contacto\Controllers\EnviarMensajeActionController;

require_once __DIR__ . '/controllers/enviar-mensaje-action.controller.php';

return [
    'prefix' => '/contacto',
    'routes' => [
        ['POST', '/enviar', EnviarMensajeActionController::class, 'enviar'],
    ],
];
