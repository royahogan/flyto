<?php

namespace App\Home\Controllers;

use App\Ciudades\Services\CiudadService;
use App\Novedades\Services\NovedadService;
use App\Shared\Http\Flash;
use App\Shared\Http\ViewResponse;
use Throwable;

require_once __DIR__ . '/../../ciudades/services/ciudad.service.php';
require_once __DIR__ . '/../../novedades/services/novedad.service.php';
require_once __DIR__ . '/../../shared/http/flash.php';
require_once __DIR__ . '/../../shared/http/view-response.php';

class HomePageController
{
    private CiudadService $ciudadService;
    private NovedadService $novedadService;
    private ViewResponse $viewResponse;

    public function __construct(
        CiudadService $ciudadService,
        NovedadService $novedadService,
        ViewResponse $viewResponse
    ) {
        $this->ciudadService = $ciudadService;
        $this->novedadService = $novedadService;
        $this->viewResponse = $viewResponse;
    }

    public function show(array $params, array $query, string $layoutPath): void
    {
        $flash = Flash::consume();
        $oldInput = Flash::consumeOld();
        $validationErrors = $flash['validationErrors'] ?? [];

        $this->viewResponse->render(
            __DIR__ . '/../views/pages/home.page.php',
            'Flyto - Reservas de vuelos',
            [
                'ciudades' => $this->loadCiudades(),
                'novedades' => $this->loadNovedades(),
                'flash' => [
                    'success' => $flash['success'] ?? null,
                    'error' => $flash['error'] ?? null,
                ],
                'oldInput' => is_array($oldInput) ? $oldInput : [],
                'validationErrors' => is_array($validationErrors) ? $validationErrors : [],
                'contactRedirectTo' => 'home',
            ],
            200,
            $layoutPath
        );
    }

    private function loadCiudades(): array
    {
        try {
            return array_map(
                fn ($ciudad) => $ciudad->toArray(),
                $this->ciudadService->getTodas()
            );
        } catch (Throwable) {
            return [];
        }
    }

    private function loadNovedades(): array
    {
        try {
            return array_map(
                fn ($novedad) => $novedad->toArray(),
                $this->novedadService->getUltimas()
            );
        } catch (Throwable) {
            return [];
        }
    }
}
