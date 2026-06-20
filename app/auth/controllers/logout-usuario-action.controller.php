<?php

namespace App\Auth\Controllers;

use App\Auth\Middlewares\AuthMiddleware;
use App\Auth\Services\LogoutUsuarioService;
use App\Auth\Services\SessionService;
use App\Shared\Http\RedirectResponse;
use Throwable;

require_once __DIR__ . '/../middlewares/auth.middleware.php';
require_once __DIR__ . '/../services/logout-usuario.service.php';
require_once __DIR__ . '/../services/session.service.php';
require_once __DIR__ . '/../../shared/http/redirect-response.php';

class LogoutUsuarioActionController
{
    private LogoutUsuarioService $logoutUsuarioService;
    private SessionService $sessionService;

    public function __construct(
        LogoutUsuarioService $logoutUsuarioService,
        SessionService $sessionService
    ) {
        $this->logoutUsuarioService = $logoutUsuarioService;
        $this->sessionService = $sessionService;
    }

    public function logout(array $params = [], array $query = []): void
    {
        try {
            $middleware = new AuthMiddleware($this->sessionService);
            $middleware->handle();

            $this->logoutUsuarioService->execute();

            RedirectResponse::to('/', [], 303);
        } catch (Throwable) {
            RedirectResponse::to('/auth/login', [], 303);
        }
    }
}
