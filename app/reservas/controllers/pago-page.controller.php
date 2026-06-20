<?php

namespace App\Reservas\Controllers;

use App\Auth\Services\SessionService;
use App\Reservas\Dtos\DatosPasajerosDto;
use App\Reservas\Services\ReservaService;
use App\Shared\Http\Flash;
use App\Shared\Http\HttpException;
use App\Shared\Http\RedirectResponse;
use App\Shared\Http\ViewResponse;

require_once __DIR__ . '/../../auth/services/session.service.php';
require_once __DIR__ . '/guardar-pasajeros-action.controller.php';
require_once __DIR__ . '/../dtos/datos-pasajeros.dto.php';
require_once __DIR__ . '/../services/reserva.service.php';
require_once __DIR__ . '/../../shared/http/flash.php';
require_once __DIR__ . '/../../shared/http/http-exception.php';
require_once __DIR__ . '/../../shared/http/redirect-response.php';
require_once __DIR__ . '/../../shared/http/view-response.php';

class PagoPageController
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
        $usuario = $this->sessionService->getUser();

        if (!is_array($usuario) || !isset($usuario['id'])) {
            Flash::error('Necesitas iniciar sesion para realizar una reserva.');
            RedirectResponse::to('/auth/login', [], 303);
            return;
        }

        $vueloId = filter_var($query['vueloId'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $datosPasajeros = $this->sessionService->get(GuardarPasajerosActionController::SESSION_KEY);

        if ($vueloId === false || !$datosPasajeros instanceof DatosPasajerosDto || $datosPasajeros->vueloId !== (int) $vueloId) {
            throw new HttpException('No hay una reserva pendiente para completar el pago.', 400);
        }

        $vuelo = $this->reservaService->obtenerVueloPendiente((int) $vueloId, $datosPasajeros->cantidadPasajeros);
        $flash = Flash::consume();
        $oldInput = Flash::consumeOld();

        $this->viewResponse->render(
            __DIR__ . '/../views/pages/pago.page.php',
            'Pago - Flyto',
            [
                'vuelo' => $vuelo,
                'datosPasajeros' => $datosPasajeros,
                'flash' => ['error' => $flash['error'] ?? null],
                'validationErrors' => is_array($flash['validationErrors'] ?? null) ? $flash['validationErrors'] : [],
                'oldInput' => is_array($oldInput) ? $oldInput : [],
            ],
            200,
            $layoutPath
        );
    }
}
