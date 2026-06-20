<?php

namespace App\Reservas\Controllers;

use App\Auth\Services\SessionService;
use App\Reservas\Services\ReservaService;
use App\Shared\Http\Flash;
use App\Shared\Http\RedirectResponse;
use App\Shared\Http\ViewResponse;

require_once __DIR__ . '/../../auth/services/session.service.php';
require_once __DIR__ . '/../services/reserva.service.php';
require_once __DIR__ . '/../../shared/http/flash.php';
require_once __DIR__ . '/../../shared/http/redirect-response.php';
require_once __DIR__ . '/../../shared/http/view-response.php';

class ConfirmacionPageController
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
            Flash::error('Necesitas iniciar sesion para ver la confirmacion.');
            RedirectResponse::to('/auth/login', [], 303);
            return;
        }

        $reservaId = filter_var($query['reservaId'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($reservaId === false) {
            Flash::error('La reserva indicada no es valida.');
            RedirectResponse::to('/mi-perfil/mis-reservas', [], 303);
            return;
        }

        $reserva = $this->reservaService->obtenerReservaUsuario((int) $reservaId, (int) $usuario['id']);

        $codigoReserva = sprintf(
            'FLY-%06d',
            $reserva->id
        );

        $this->viewResponse->render(
            __DIR__ . '/../views/pages/confirmacion.page.php',
            'Reserva confirmada - Flyto',
            [
                'reserva' => $reserva,
                'codigoReserva' => $codigoReserva,
                'correoConfirmacion' => (string) ($reserva->usuario['email'] ?? $usuario['email'] ?? ''),
            ],
            200,
            $layoutPath
        );
    }
}
