<?php

namespace App\Perfil\Controllers;

use App\Shared\Http\Flash;
use App\Shared\Http\ViewResponse;

require_once __DIR__ . '/../../shared/http/flash.php';
require_once __DIR__ . '/../../shared/http/view-response.php';

class MiPerfilPageController
{
    private ViewResponse $viewResponse;

    public function __construct(ViewResponse $viewResponse)
    {
        $this->viewResponse = $viewResponse;
    }

    public function show(array $params = [], array $query = [], ?string $layoutPath = null): void
    {
        $flash = Flash::consume();

        $this->viewResponse->render(
            __DIR__ . '/../views/pages/mi-perfil.page.php',
            'Mi perfil - Flyto',
            ['reservaFeedback' => $flash['success'] ?? $flash['error'] ?? null],
            200,
            $layoutPath ?? __DIR__ . '/../../shared/views/layouts/public.layout.php'
        );
    }
}
