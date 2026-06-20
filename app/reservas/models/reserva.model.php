<?php

namespace App\Reservas\Models;

use App\Vuelos\Models\Vuelo;

require_once __DIR__ . '/../../vuelos/models/vuelo.model.php';
require_once __DIR__ . '/pasajero.model.php';
require_once __DIR__ . '/metodo-pago.model.php';

class Reserva
{
    public ?int $id;
    public array $usuario;
    public Vuelo $vuelo;
    public float $precioTotal;
    public \DateTime $fechaReserva;
    public string $estado;
    /** @var Pasajero[] */
    public array $pasajeros;
    public MetodoPago $metodoPago;

    public function __construct(
        ?int $id,
        array $usuario,
        Vuelo $vuelo,
        float $precioTotal,
        \DateTime $fechaReserva,
        string $estado,
        array $pasajeros,
        MetodoPago $metodoPago
    ) {
        $this->id = $id;
        $this->usuario = $usuario;
        $this->vuelo = $vuelo;
        $this->precioTotal = $precioTotal;
        $this->fechaReserva = $fechaReserva;
        $this->estado = $estado;
        $this->pasajeros = $pasajeros;
        $this->metodoPago = $metodoPago;
    }
}
