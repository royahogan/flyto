<?php

namespace App\Vuelos\Controllers;

use App\Shared\Http\JsonResponse;
use App\Vuelos\Dtos\BuscarVuelosDto;
use App\Vuelos\Services\VueloService;
use App\Vuelos\Validators\BuscarVueloValidator;
use Throwable;

require_once __DIR__ . '/../../shared/http/json-response.php';
require_once __DIR__ . '/../dtos/buscar-vuelos.dto.php';
require_once __DIR__ . '/../services/vuelo.service.php';
require_once __DIR__ . '/../validators/buscar-vuelo.validator.php';

class VueloController
{
    private VueloService $vueloService;

    public function __construct(VueloService $vueloService)
    {
        $this->vueloService = $vueloService;
    }

    public function buscar(array $params = [], array $query = []): void
    {
        try {
            BuscarVueloValidator::validate($_GET);

            $dto = BuscarVuelosDto::fromArray($_GET);

            $resultado = $this->vueloService->buscar($dto);

            JsonResponse::success([
                'criterios' => [
                    'origen' => $resultado['criterios']->origen,
                    'destino' => $resultado['criterios']->destino,
                    'fechaSalida' => $resultado['criterios']->fechaSalida,
                    'cantidadPasajeros' => $resultado['criterios']->cantidadPasajeros,
                    'precioMaximo' => $resultado['criterios']->precioMaximo,
                    'aerolineas' => $resultado['criterios']->aerolineas,
                    'orden' => $resultado['criterios']->orden,
                ],
                'vuelos' => array_map(fn ($vuelo) => $vuelo->toArray(), $resultado['vuelos']),
                'aerolineas' => $resultado['aerolineas'],
                'precioMaximoDisponible' => $resultado['precioMaximoDisponible'],
                'precioMaximoSeleccionado' => $resultado['precioMaximoSeleccionado'],
                'total' => $resultado['total'],
            ]);
        } catch (Throwable $exception) {
            JsonResponse::exception($exception);
        }
    }
}
