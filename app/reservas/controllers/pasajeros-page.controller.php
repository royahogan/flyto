<?php

namespace App\Reservas\Controllers;

use App\Auth\Services\SessionService;
use App\Reservas\Services\ReservaService;
use App\Reservas\Validators\PasajerosValidator;
use App\Shared\Http\Flash;
use App\Shared\Http\RedirectResponse;
use App\Shared\Http\ViewResponse;

require_once __DIR__ . '/../../auth/services/session.service.php';
require_once __DIR__ . '/../services/reserva.service.php';
require_once __DIR__ . '/../validators/pasajeros.validator.php';
require_once __DIR__ . '/../../shared/http/flash.php';
require_once __DIR__ . '/../../shared/http/redirect-response.php';
require_once __DIR__ . '/../../shared/http/view-response.php';

class PasajerosPageController
{
    public function __construct(
        private ReservaService $reservaService,
        private SessionService $sessionService,
        private ViewResponse $viewResponse
    ) {
    }

    public function show(array $params, array $query, string $layoutPath): void
    {
        $this->sessionService->start();
        if (!$this->sessionService->isAuthenticated()) {
            Flash::error('Necesitas iniciar sesion para realizar una reserva.');
            RedirectResponse::to('/auth/login', [], 303);
            return;
        }

        PasajerosValidator::validateQuery($_GET);
        $vueloId = (int) $_GET['vueloId'];
        $cantidadPasajeros = (int) $_GET['cantidadPasajeros'];
        $vuelo = $this->reservaService->obtenerVueloPendiente($vueloId, $cantidadPasajeros);
        $flash = Flash::consume();
        $oldInput = Flash::consumeOld();

        $this->viewResponse->render(
            __DIR__ . '/../views/pages/pasajeros.page.php',
            'Datos de los pasajeros - Flyto',
            [
                'vuelo' => $vuelo,
                'cantidadPasajeros' => $cantidadPasajeros,
                'flash' => ['error' => $flash['error'] ?? null],
                'validationErrors' => is_array($flash['validationErrors'] ?? null) ? $flash['validationErrors'] : [],
                'oldInput' => is_array($oldInput) ? $oldInput : [],
            ],
            200,
            $layoutPath
        );
    }
}
