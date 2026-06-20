<?php

namespace App\Novedades\Controllers;

use App\Novedades\Services\NovedadService;
use App\Shared\Http\Flash;
use App\Shared\Http\ViewResponse;
use Throwable;

require_once __DIR__ . '/../services/novedad.service.php';
require_once __DIR__ . '/../../shared/http/flash.php';
require_once __DIR__ . '/../../shared/http/view-response.php';

class NovedadesPageController
{
    private NovedadService $novedadService;
    private ViewResponse $viewResponse;

    public function __construct(NovedadService $novedadService, ViewResponse $viewResponse)
    {
        $this->novedadService = $novedadService;
        $this->viewResponse = $viewResponse;
    }

    public function show(array $params, array $query, string $layoutPath): void
    {
        $flash = Flash::consume();
        $oldInput = Flash::consumeOld();
        $validationErrors = $flash['validationErrors'] ?? [];

        try {
            $novedades = array_map(
                fn ($novedad) => $novedad->toArray(),
                $this->novedadService->getVigentes()
            );
        } catch (Throwable) {
            $novedades = [];
            $flash['error'] = 'No pudimos cargar las novedades. Intentalo nuevamente en unos minutos.';
        }

        $this->viewResponse->render(
            __DIR__ . '/../views/pages/novedades.page.php',
            'Novedades - Flyto',
            [
                'novedades' => $novedades,
                'flash' => [
                    'success' => $flash['success'] ?? null,
                    'error' => $flash['error'] ?? null,
                ],
                'oldInput' => is_array($oldInput) ? $oldInput : [],
                'validationErrors' => is_array($validationErrors) ? $validationErrors : [],
            ],
            200,
            $layoutPath
        );
    }
}
