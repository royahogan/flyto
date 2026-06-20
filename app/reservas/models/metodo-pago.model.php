<?php

namespace App\Reservas\Models;

class MetodoPago
{
    public ?int $id;
    public string $nombreTitular;
    public string $ultimosCuatroDigitos;
    public int $vencimientoMes;
    public int $vencimientoAnio;
    public \DateTime $fechaPago;

    public function __construct(
        ?int $id,
        string $nombreTitular,
        string $ultimosCuatroDigitos,
        int $vencimientoMes,
        int $vencimientoAnio,
        \DateTime $fechaPago
    ) {
        $this->id = $id;
        $this->nombreTitular = $nombreTitular;
        $this->ultimosCuatroDigitos = $ultimosCuatroDigitos;
        $this->vencimientoMes = $vencimientoMes;
        $this->vencimientoAnio = $vencimientoAnio;
        $this->fechaPago = $fechaPago;
    }
}
