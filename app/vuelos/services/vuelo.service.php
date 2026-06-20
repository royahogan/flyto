<?php

namespace App\Vuelos\Services;

use App\Vuelos\Dtos\BuscarVuelosDto;
use App\Vuelos\Models\Vuelo;
use App\Vuelos\Repositories\VueloRepository;

require_once __DIR__ . '/../dtos/buscar-vuelos.dto.php';
require_once __DIR__ . '/../models/vuelo.model.php';
require_once __DIR__ . '/../repositories/vuelo.repository.php';

class VueloService
{
    private VueloRepository $vueloRepository;

    public function __construct(VueloRepository $vueloRepository)
    {
        $this->vueloRepository = $vueloRepository;
    }

    public function buscar(BuscarVuelosDto $dto): array
    {
        $base = $this->buscarBase($dto->sinFiltros());
        $precioMaximoDisponible = $this->precioMaximo($base);
        $precioMaximoSeleccionado = $dto->precioMaximo ?? $precioMaximoDisponible;
        $vuelos = $this->aplicarFiltros($base, $precioMaximoSeleccionado, $dto->aerolineas);
        $this->ordenar($vuelos, $dto->orden);

        return [
            'criterios' => $dto,
            'vuelos' => $vuelos,
            'aerolineas' => $this->aerolineasDisponibles($base),
            'precioMaximoDisponible' => $precioMaximoDisponible,
            'precioMaximoSeleccionado' => $precioMaximoSeleccionado,
            'total' => count($vuelos),
        ];
    }

    /**
     * @return Vuelo[]
     */
    private function buscarBase(BuscarVuelosDto $dto): array
    {
        return $this->vueloRepository->buscarDisponibles($dto);
    }

    /**
     * @param Vuelo[] $vuelos
     * @return Vuelo[]
     */
    private function aplicarFiltros(array $vuelos, ?int $precioMaximo, array $aerolineas): array
    {
        return array_values(array_filter($vuelos, function (Vuelo $vuelo) use ($precioMaximo, $aerolineas) {
            if ($precioMaximo !== null && $vuelo->precio > $precioMaximo) {
                return false;
            }

            if ($aerolineas !== [] && !in_array($vuelo->aerolinea['codigoIataAerolinea'], $aerolineas, true)) {
                return false;
            }

            return true;
        }));
    }

    /**
     * @param Vuelo[] $vuelos
     */
    private function ordenar(array &$vuelos, string $orden): void
    {
        usort($vuelos, function (Vuelo $a, Vuelo $b) use ($orden) {
            return match ($orden) {
                'duracion' => $a->duracionMinutos() <=> $b->duracionMinutos(),
                'salida' => $a->fechaSalida <=> $b->fechaSalida,
                default => $a->precio <=> $b->precio,
            };
        });
    }

    /**
     * @param Vuelo[] $vuelos
     */
    private function precioMaximo(array $vuelos): float
    {
        if ($vuelos === []) {
            return 0;
        }

        return max(array_map(fn (Vuelo $vuelo) => $vuelo->precio, $vuelos));
    }

    /**
     * @param Vuelo[] $vuelos
     */
    private function aerolineasDisponibles(array $vuelos): array
    {
        $aerolineas = [];

        foreach ($vuelos as $vuelo) {
            $codigoIata = $vuelo->aerolinea['codigoIataAerolinea'];
            $aerolineas[$codigoIata] = [
                'id' => $vuelo->aerolinea['idAerolinea'],
                'nombre' => $vuelo->aerolinea['nombreAerolinea'],
                'codigoIata' => $codigoIata,
            ];
        }

        uasort($aerolineas, fn (array $a, array $b) => $a['nombre'] <=> $b['nombre']);

        return array_values($aerolineas);
    }
}
