<?php

namespace App\Auth\Controllers;

use App\Shared\Http\Flash;
use App\Shared\Http\RedirectResponse;
use App\Shared\Http\ViewResponse;
use App\Auth\Services\SessionService;

require_once __DIR__ . '/../services/session.service.php';
require_once __DIR__ . '/../../shared/http/flash.php';
require_once __DIR__ . '/../../shared/http/redirect-response.php';
require_once __DIR__ . '/../../shared/http/view-response.php';

class RegistroPageController
{
    private SessionService $sessionService;
    private ViewResponse $viewResponse;

    public function __construct(SessionService $sessionService, ViewResponse $viewResponse)
    {
        $this->sessionService = $sessionService;
        $this->viewResponse = $viewResponse;
    }

    public function show(array $params, array $query, string $layoutPath): void
    {
        $this->sessionService->start();

        if ($this->sessionService->isAuthenticated()) {
            RedirectResponse::to('/');
            return;
        }

        $flash = Flash::consume();
        $oldInput = Flash::consumeOld();
        $validationErrors = $flash['validationErrors'] ?? [];

        $this->viewResponse->render(
            __DIR__ . '/../views/pages/registro.page.php',
            'Registrarse - Flyto',
            [
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
