<?php

namespace App\Vuelos\Models;

class Vuelo
{
    public int $id;
    public string $codigoVuelo;
    public \DateTime $fechaSalida;
    public \DateTime $fechaLlegada;
    public \DateTime $fechaCreacion;
    public float $precio;
    public int $distancia;
    public float $duracion;
    public string $estado;
    public int $asientosDisponibles;
    public int $asientosOcupados;
    public array $ciudadOrigen;
    public array $ciudadDestino;
    public array $aerolinea;
    public ?array $promocion;

    public function __construct(
        int $id,
        string $codigoVuelo,
        \DateTime $fechaSalida,
        \DateTime $fechaLlegada,
        \DateTime $fechaCreacion,
        float $precio,
        int $distancia,
        float $duracion,
        string $estado,
        int $asientosDisponibles,
        int $asientosOcupados,
        array $ciudadOrigen,
        array $ciudadDestino,
        array $aerolinea,
        ?array $promocion
    ) {
        $this->id = $id;
        $this->codigoVuelo = $codigoVuelo;
        $this->fechaSalida = $fechaSalida;
        $this->fechaLlegada = $fechaLlegada;
        $this->fechaCreacion = $fechaCreacion;
        $this->precio = $precio;
        $this->distancia = $distancia;
        $this->duracion = $duracion;
        $this->estado = $estado;
        $this->asientosDisponibles = $asientosDisponibles;
        $this->asientosOcupados = $asientosOcupados;
        $this->ciudadOrigen = $ciudadOrigen;
        $this->ciudadDestino = $ciudadDestino;
        $this->aerolinea = $aerolinea;
        $this->promocion = $promocion;
    }

    public function asientosLibres(): int
    {
        return max(0, $this->asientosDisponibles - $this->asientosOcupados);
    }

    public function duracionMinutos(): int
    {
        return (int) round($this->duracion * 60);
    }

    public function duracionTexto(): string
    {
        $minutes = $this->duracionMinutos();
        $hours = intdiv($minutes, 60);
        $remainingMinutes = $minutes % 60;

        return sprintf('%dh %02dm', $hours, $remainingMinutes);
    }

    public function precioConPromocion(): float
    {
        if ($this->promocion === null) {
            return $this->precio;
        }

        return $this->precio * (1 - (float) $this->promocion['descuento']);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'codigoVuelo' => $this->codigoVuelo,
            'fechaSalida' => $this->fechaSalida->format('Y-m-d H:i:s'),
            'fechaLlegada' => $this->fechaLlegada->format('Y-m-d H:i:s'),
            'fechaCreacion' => $this->fechaCreacion->format('Y-m-d H:i:s'),
            'horaSalida' => $this->fechaSalida->format('H:i'),
            'horaLlegada' => $this->fechaLlegada->format('H:i'),
            'precio' => $this->precio,
            'distancia' => $this->distancia,
            'duracion' => $this->duracion,
            'duracionTexto' => $this->duracionTexto(),
            'estado' => $this->estado,
            'asientosDisponibles' => $this->asientosDisponibles,
            'asientosOcupados' => $this->asientosOcupados,
            'asientosLibres' => $this->asientosLibres(),
            'ciudadOrigen' => $this->ciudadOrigen,
            'ciudadDestino' => $this->ciudadDestino,
            'aerolinea' => $this->aerolinea,
            'promocion' => $this->promocion
        ];
    }
}
