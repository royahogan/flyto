<?php

namespace App\Reservas\Controllers;

use App\Auth\Services\SessionService;
use App\Reservas\Dtos\CrearReservaDto;
use App\Reservas\Dtos\DatosPasajerosDto;
use App\Reservas\Models\MetodoPago;
use App\Reservas\Models\Pasajero;
use App\Reservas\Services\ReservaService;
use App\Reservas\Validators\ReservaValidator;
use App\Shared\Http\Flash;
use App\Shared\Http\HttpException;
use App\Shared\Http\RedirectResponse;
use Throwable;

require_once __DIR__ . '/../../auth/services/session.service.php';
require_once __DIR__ . '/../dtos/crear-reserva.dto.php';
require_once __DIR__ . '/../dtos/datos-pasajeros.dto.php';
require_once __DIR__ . '/guardar-pasajeros-action.controller.php';
require_once __DIR__ . '/../services/reserva.service.php';
require_once __DIR__ . '/../validators/reserva.validator.php';
require_once __DIR__ . '/../../shared/http/flash.php';
require_once __DIR__ . '/../../shared/http/http-exception.php';
require_once __DIR__ . '/../../shared/http/redirect-response.php';

class CrearReservaActionController
{
    private ReservaService $reservaService;
    private SessionService $sessionService;

    public function __construct(ReservaService $reservaService, SessionService $sessionService)
    {
        $this->reservaService = $reservaService;
        $this->sessionService = $sessionService;
    }

    public function crear(array $params = [], array $query = []): void
    {
        $this->sessionService->start();
        $usuario = $this->sessionService->getUser();

        if (!is_array($usuario) || !isset($usuario['id'])) {
            Flash::error('Necesitas iniciar sesion para realizar una reserva.');
            RedirectResponse::to('/auth/login', [], 303);
            return;
        }

        $datosPasajeros = $this->sessionService->get(GuardarPasajerosActionController::SESSION_KEY);
        if (!$datosPasajeros instanceof DatosPasajerosDto) {
            Flash::error('No hay datos de pasajeros para completar la reserva.');
            RedirectResponse::to('/vuelos/buscar', [], 303);
            return;
        }

        $vueloId = filter_var($_POST['vueloId'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($vueloId === false || $datosPasajeros->vueloId !== (int) $vueloId) {
            Flash::error('El vuelo de la reserva no es valido.');
            RedirectResponse::to('/reservas/pago', ['vueloId' => $datosPasajeros->vueloId], 303);
            return;
        }

        try {
            ReservaValidator::validatePaymentForm($_POST);

            $pagoForm = $_POST['pago'];
            [$vencimientoMes, $vencimientoAnioCorto] = array_map('intval', explode('/', (string) $pagoForm['vencimiento']));
            $numeroTarjeta = preg_replace('/\s+/', '', (string) $pagoForm['numeroTarjeta']);
            $data = [
                'vueloId' => (int) $vueloId,
                'pasajeros' => $datosPasajeros->pasajeros,
                'pago' => [
                    'nombreTitular' => $pagoForm['nombreTitular'],
                    'numeroTarjeta' => $numeroTarjeta,
                    'vencimientoMes' => $vencimientoMes,
                    'vencimientoAnio' => 2000 + $vencimientoAnioCorto,
                ],
            ];
            ReservaValidator::validate($data);

            $pasajeros = array_map(
                fn (array $pasajero) => new Pasajero(
                    id: null,
                    nombre: trim((string) $pasajero['nombre']),
                    apellido: trim((string) $pasajero['apellido']),
                    documento: trim((string) $pasajero['documento']),
                    pasaporte: trim((string) $pasajero['pasaporte']),
                    fechaNacimiento: new \DateTime((string) $pasajero['fechaNacimiento']),
                    telefonoContacto: trim((string) $pasajero['telefonoContacto']),
                    correoElectronico: trim((string) $pasajero['correoElectronico'])
                ),
                $data['pasajeros']
            );

            $metodoPago = new MetodoPago(
                id: null,
                nombreTitular: trim((string) $data['pago']['nombreTitular']),
                ultimosCuatroDigitos: substr($numeroTarjeta, -4),
                vencimientoMes: (int) $data['pago']['vencimientoMes'],
                vencimientoAnio: (int) $data['pago']['vencimientoAnio'],
                fechaPago: new \DateTime()
            );

            $dto = new CrearReservaDto(
                usuarioId: (int) $usuario['id'],
                vueloId: (int) $data['vueloId'],
                pasajeros: $pasajeros,
                metodoPago: $metodoPago
            );

            $reserva = $this->reservaService->crear($dto);
            $this->sessionService->remove(GuardarPasajerosActionController::SESSION_KEY);
            RedirectResponse::to('/reservas/confirmacion', ['reservaId' => $reserva->id], 303);
            return;
        } catch (HttpException $exception) {
            Flash::error('Revisa los datos del metodo de pago e intentalo nuevamente.');
            Flash::validationErrors($this->validationErrorsFromException($exception));
        } catch (Throwable) {
            Flash::error('No pudimos realizar la reserva. Intentalo nuevamente en unos minutos.');
        }

        Flash::old([
            'pago' => [
                'nombreTitular' => trim((string) ($_POST['pago']['nombreTitular'] ?? '')),
                'vencimiento' => trim((string) ($_POST['pago']['vencimiento'] ?? '')),
                'aceptaTerminos' => (string) ($_POST['pago']['aceptaTerminos'] ?? ''),
            ],
        ]);
        RedirectResponse::to('/reservas/pago', ['vueloId' => $datosPasajeros->vueloId], 303);
    }

    private function validationErrorsFromException(HttpException $exception): array
    {
        $field = $exception->getDetails()['field'] ?? null;

        if ($field === 'pago.vencimientoMes' || $field === 'pago.vencimientoAnio') {
            $field = 'pago.vencimiento';
        }

        return is_string($field) && $field !== ''
            ? [$field => $exception->getMessage()]
            : ['general' => $exception->getMessage()];
    }
}
