<?php

namespace App\Reservas\Dtos;

use App\Reservas\Models\MetodoPago;

require_once __DIR__ . '/../models/pasajero.model.php';
require_once __DIR__ . '/../models/metodo-pago.model.php';

class CrearReservaDto
{
    public int $usuarioId;
    public int $vueloId;
    /** @var \App\Reservas\Models\Pasajero[] */
    public array $pasajeros;
    public MetodoPago $metodoPago;

    public function __construct(
        int $usuarioId,
        int $vueloId,
        array $pasajeros,
        MetodoPago $metodoPago
    ) {
        $this->usuarioId = $usuarioId;
        $this->vueloId = $vueloId;
        $this->pasajeros = $pasajeros;
        $this->metodoPago = $metodoPago;
    }
}
