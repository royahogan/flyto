<?php

namespace App\Ciudades\Models;

class Ciudad
{
    public int $id;
    public string $nombre;
    public string $abreviacion;
    public int $paisId;
    public string $nombrePais;

    public function __construct(
        int $id,
        string $nombre,
        string $abreviacion,
        int $paisId,
        string $nombrePais
    ) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->abreviacion = $abreviacion;
        $this->paisId = $paisId;
        $this->nombrePais = $nombrePais;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'abreviacion' => $this->abreviacion,
            'paisId' => $this->paisId,
            'nombrePais' => $this->nombrePais,
        ];
    }
}
