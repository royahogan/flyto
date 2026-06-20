<?php

namespace App\Reservas\Dtos;

class DatosPasajerosDto
{
    public int $vueloId;
    public int $cantidadPasajeros;
    public array $pasajeros;

    public function __construct(int $vueloId, int $cantidadPasajeros, array $pasajeros)
    {
        $this->vueloId = $vueloId;
        $this->cantidadPasajeros = $cantidadPasajeros;
        $this->pasajeros = $pasajeros;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            vueloId: (int) $data['vueloId'],
            cantidadPasajeros: (int) $data['cantidadPasajeros'],
            pasajeros: array_values(array_map(
                static fn (array $pasajero): array => [
                    'nombre' => trim((string) $pasajero['nombre']),
                    'apellido' => trim((string) $pasajero['apellido']),
                    'documento' => trim((string) $pasajero['documento']),
                    'pasaporte' => trim((string) $pasajero['pasaporte']),
                    'fechaNacimiento' => (string) $pasajero['fechaNacimiento'],
                    'nacionalidad' => trim((string) $pasajero['nacionalidad']),
                    'correoElectronico' => trim((string) $pasajero['correoElectronico']),
                    'telefonoContacto' => trim((string) $pasajero['telefonoContacto']),
                ],
                $data['pasajeros']
            ))
        );
    }
}
