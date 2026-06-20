<?php

namespace App\Reservas\Controllers;

use App\Auth\Services\SessionService;
use App\Reservas\Dtos\DatosPasajerosDto;
use App\Reservas\Services\ReservaService;
use App\Reservas\Validators\PasajerosValidator;
use App\Shared\Http\Flash;
use App\Shared\Http\HttpException;
use App\Shared\Http\RedirectResponse;
use Throwable;

require_once __DIR__ . '/../../auth/services/session.service.php';
require_once __DIR__ . '/../dtos/datos-pasajeros.dto.php';
require_once __DIR__ . '/../services/reserva.service.php';
require_once __DIR__ . '/../validators/pasajeros.validator.php';
require_once __DIR__ . '/../../shared/http/flash.php';
require_once __DIR__ . '/../../shared/http/http-exception.php';
require_once __DIR__ . '/../../shared/http/redirect-response.php';

class GuardarPasajerosActionController
{
    public const SESSION_KEY = 'reserva_datos_pasajeros';

    public function __construct(
        private ReservaService $reservaService,
        private SessionService $sessionService
    ) {
    }

    public function guardar(array $params = [], array $query = []): void
    {
        $this->sessionService->start();
        if (!$this->sessionService->isAuthenticated()) {
            Flash::error('Necesitas iniciar sesion para realizar una reserva.');
            RedirectResponse::to('/auth/login', [], 303);
            return;
        }

        $vueloId = filter_var($_POST['vueloId'] ?? null, FILTER_VALIDATE_INT) ?: 0;
        $cantidad = filter_var($_POST['cantidadPasajeros'] ?? null, FILTER_VALIDATE_INT) ?: 0;

        try {
            PasajerosValidator::validate($_POST);
            $this->reservaService->obtenerVueloPendiente($vueloId, $cantidad);
            $dto = DatosPasajerosDto::fromArray($_POST);
            $this->sessionService->set(self::SESSION_KEY, $dto);

            RedirectResponse::to('/reservas/pago', ['vueloId' => $vueloId], 303);
        } catch (HttpException $exception) {
            Flash::error('Revisa los datos de los pasajeros e intentalo nuevamente.');
            Flash::validationErrors($this->validationErrorsFromException($exception));
            Flash::old($_POST);
            RedirectResponse::to('/reservas/pasajeros', [
                'vueloId' => max(1, $vueloId),
                'cantidadPasajeros' => min(4, max(1, $cantidad)),
            ], 303);
        } catch (Throwable) {
            Flash::error('No pudimos guardar los pasajeros. Intentalo nuevamente en unos minutos.');
            Flash::old($_POST);
            RedirectResponse::to('/reservas/pasajeros', [
                'vueloId' => max(1, $vueloId),
                'cantidadPasajeros' => min(4, max(1, $cantidad)),
            ], 303);
        }
    }

    private function validationErrorsFromException(HttpException $exception): array
    {
        $field = $exception->getDetails()['field'] ?? null;

        return is_string($field) && $field !== ''
            ? [$field => $exception->getMessage()]
            : ['general' => $exception->getMessage()];
    }
}
