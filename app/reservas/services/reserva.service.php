<?php

namespace App\Reservas\Services;

use App\Reservas\Dtos\CrearReservaDto;
use App\Reservas\Models\Reserva;
use App\Reservas\Repositories\ReservaRepository;
use App\Shared\Http\HttpException;
use App\Vuelos\Models\Vuelo;

require_once __DIR__ . '/../dtos/crear-reserva.dto.php';
require_once __DIR__ . '/../models/reserva.model.php';
require_once __DIR__ . '/../repositories/reserva.repository.php';
require_once __DIR__ . '/../../shared/http/http-exception.php';

class ReservaService
{
    private const ESTADOS = ['confirmada', 'cancelada', 'completada'];

    private ReservaRepository $reservaRepository;

    public function __construct(ReservaRepository $reservaRepository)
    {
        $this->reservaRepository = $reservaRepository;
    }

    public function obtenerVueloPendiente(int $vueloId, int $cantidadPasajeros): Vuelo
    {
        $vuelo = $this->reservaRepository->findVueloParaReserva($vueloId);

        if (!$vuelo) {
            throw new HttpException('El vuelo seleccionado no existe.', 404, ['field' => 'vueloId']);
        }

        if (strtolower($vuelo->estado) !== 'pendiente' || $vuelo->fechaSalida <= new \DateTime()) {
            throw new HttpException('El vuelo seleccionado ya no esta disponible.', 409, ['field' => 'vueloId']);
        }

        if ($vuelo->asientosLibres() < $cantidadPasajeros) {
            throw new HttpException('No hay suficientes asientos disponibles.', 409, ['field' => 'cantidadPasajeros']);
        }

        return $vuelo;
    }

    /** @return Reserva[] */
    public function listarReservasUsuario(int $usuarioId, ?string $estado = null): array
    {
        $reservas = $this->reservaRepository->getPorUsuario($usuarioId);

        if ($estado === null || trim($estado) === '') {
            return $reservas;
        }

        $estado = strtolower(trim($estado));
        if (!in_array($estado, self::ESTADOS, true)) {
            throw new HttpException('El estado indicado no es valido.', 400, ['field' => 'estado']);
        }

        return array_values(array_filter(
            $reservas,
            fn (Reserva $reserva) => strtolower($reserva->estado) === $estado
        ));
    }

    public function crear(CrearReservaDto $dto): Reserva
    {
        $cantidadPasajeros = count($dto->pasajeros);
        if ($cantidadPasajeros < 1) {
            throw new HttpException('Debes incluir al menos un pasajero.', 400, ['field' => 'pasajeros']);
        }
        $vuelo = $this->obtenerVueloPendiente($dto->vueloId, $cantidadPasajeros);

        $ahora = new \DateTime();
        $vencimientoActual = ((int) $ahora->format('Y') * 100) + (int) $ahora->format('n');
        $vencimientoPago = ($dto->metodoPago->vencimientoAnio * 100) + $dto->metodoPago->vencimientoMes;

        if ($vencimientoPago < $vencimientoActual) {
            throw new HttpException('La tarjeta se encuentra vencida.', 400, ['field' => 'pago.vencimientoMes']);
        }

        $reserva = new Reserva(
            id: null,
            usuario: ['id' => $dto->usuarioId],
            vuelo: $vuelo,
            precioTotal: round($vuelo->precioConPromocion() * $cantidadPasajeros, 2),
            fechaReserva: $ahora,
            estado: 'confirmada',
            pasajeros: $dto->pasajeros,
            metodoPago: $dto->metodoPago
        );

        $this->reservaRepository->create($reserva);

        return $this->reservaRepository->findById((int) $reserva->id) ?? $reserva;
    }

    public function obtenerReservaUsuario(int $reservaId, int $usuarioId): Reserva
    {
        $reserva = $this->reservaRepository->findById($reservaId);

        if (!$reserva || (int) $reserva->usuario['id'] !== $usuarioId) {
            throw new HttpException('Reserva no encontrada.', 404);
        }

        return $reserva;
    }

    public function puedeCancelar(Reserva $reserva): bool
    {
        return strtolower($reserva->estado) === 'confirmada'
            && $reserva->vuelo->fechaSalida >= (new \DateTime())->modify('+72 hours');
    }

    public function cancelar(int $reservaId, int $usuarioId): Reserva
    {
        $reserva = $this->reservaRepository->findById($reservaId);

        if (!$reserva || (int) $reserva->usuario['id'] !== $usuarioId) {
            throw new HttpException('Reserva no encontrada.', 404);
        }

        if (strtolower($reserva->estado) !== 'confirmada') {
            throw new HttpException('Esta reserva ya no puede cancelarse.', 409);
        }

        if (!$this->puedeCancelar($reserva)) {
            throw new HttpException('La reserva solo puede cancelarse hasta 72 horas antes del vuelo.', 409);
        }

        $reserva->estado = 'cancelada';
        $this->reservaRepository->update($reserva);

        return $this->reservaRepository->findById($reservaId) ?? $reserva;
    }
}
