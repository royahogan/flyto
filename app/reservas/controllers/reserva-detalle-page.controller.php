<?php

namespace App\Reservas\Controllers;

use App\Auth\Services\SessionService;
use App\Reservas\Services\ReservaService;
use App\Shared\Http\Flash;
use App\Shared\Http\HttpException;
use App\Shared\Http\ViewResponse;

require_once __DIR__ . '/../../auth/services/session.service.php';
require_once __DIR__ . '/../services/reserva.service.php';
require_once __DIR__ . '/../../shared/http/flash.php';
require_once __DIR__ . '/../../shared/http/http-exception.php';
require_once __DIR__ . '/../../shared/http/view-response.php';

class ReservaDetallePageController
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
        $flash = Flash::consume();
        $usuario = $this->sessionService->getUser();
        $reservaId = filter_var($query['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $reserva = null;
        $puedeCancelar = false;
        $error = null;
        $statusCode = 200;

        if ($reservaId === false) {
            $error = 'La reserva indicada no es valida.';
            $statusCode = 400;
        } else {
            try {
                $reserva = $this->reservaService->obtenerReservaUsuario((int) $reservaId, (int) ($usuario['id'] ?? 0));
                $puedeCancelar = $this->reservaService->puedeCancelar($reserva);
            } catch (HttpException $exception) {
                $error = $exception->getMessage();
                $statusCode = $exception->getStatusCode();
            }
        }

        $this->viewResponse->render(
            __DIR__ . '/../views/pages/reserva-detalle.page.php',
            'Detalle de reserva - Flyto',
            [
                'reserva' => $reserva,
                'puedeCancelar' => $puedeCancelar,
                'error' => $error,
                'flash' => [
                    'success' => $flash['success'] ?? null,
                    'error' => $flash['error'] ?? null,
                ],
            ],
            $statusCode,
            $layoutPath
        );
    }
}
