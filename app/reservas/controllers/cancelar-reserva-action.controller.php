<?php

namespace App\Reservas\Controllers;

use App\Auth\Services\SessionService;
use App\Reservas\Services\ReservaService;
use App\Shared\Http\Flash;
use App\Shared\Http\HttpException;
use App\Shared\Http\RedirectResponse;
use Throwable;

require_once __DIR__ . '/../../auth/services/session.service.php';
require_once __DIR__ . '/../services/reserva.service.php';
require_once __DIR__ . '/../../shared/http/flash.php';
require_once __DIR__ . '/../../shared/http/http-exception.php';
require_once __DIR__ . '/../../shared/http/redirect-response.php';

class CancelarReservaActionController
{
    public function __construct(
        private ReservaService $reservaService,
        private SessionService $sessionService
    ) {
    }

    public function cancelar(array $params = [], array $query = []): void
    {
        $this->sessionService->start();

        if (!$this->sessionService->isAuthenticated()) {
            Flash::error('Necesitas iniciar sesion para cancelar una reserva.');
            RedirectResponse::to('/auth/login', [], 303);
            return;
        }

        $reservaId = filter_var(
            $_POST['reservaId'] ?? null,
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1]]
        );

        if ($reservaId === false) {
            Flash::error('La reserva indicada no es valida.');
            RedirectResponse::to('/mi-perfil/mis-reservas', [], 303);
            return;
        }

        try {
            $usuario = $this->sessionService->getUser();
            $this->reservaService->cancelar((int) $reservaId, (int) ($usuario['id'] ?? 0));

            Flash::success('La reserva fue cancelada correctamente.');
        } catch (HttpException $exception) {
            Flash::error($exception->getMessage());
        } catch (Throwable) {
            Flash::error('No pudimos cancelar la reserva. Intentalo nuevamente en unos minutos.');
        }

        RedirectResponse::to('/reservas/detalle', ['id' => (int) $reservaId], 303);
    }
}
