<?php

namespace App\Reservas\Controllers;

use App\Auth\Services\SessionService;
use App\Reservas\Services\ReservaService;
use App\Shared\Http\ViewResponse;

require_once __DIR__ . '/../../auth/services/session.service.php';
require_once __DIR__ . '/../services/reserva.service.php';
require_once __DIR__ . '/../../shared/http/view-response.php';

class MisReservasPageController
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
        $estado = strtolower(trim((string) ($query['estado'] ?? 'todas')));

        if (!in_array($estado, ['todas', 'confirmada', 'completada', 'cancelada'], true)) {
            $estado = 'todas';
        }

        $reservas = $this->reservaService->listarReservasUsuario(
            (int) ($usuario['id'] ?? 0),
            $estado === 'todas' ? null : $estado
        );

        $this->viewResponse->render(
            __DIR__ . '/../views/pages/mis-reservas.page.php',
            'Mis reservas - Flyto',
            [
                'reservas' => $reservas,
                'estadoSeleccionado' => $estado,
            ],
            200,
            $layoutPath
        );
    }
}
