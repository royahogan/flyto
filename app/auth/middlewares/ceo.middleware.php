<?php

namespace App\Auth\Middlewares;

use App\Auth\Services\SessionService;
use App\Shared\Http\HttpException;

require_once __DIR__ . '/../services/session.service.php';
require_once __DIR__ . '/../../shared/http/http-exception.php';

class CeoMiddleware
{
    private SessionService $sessionService;

    public function __construct(SessionService $sessionService)
    {
        $this->sessionService = $sessionService;
    }

    public function handle(): void
    {
        $this->sessionService->start();

        if (!$this->sessionService->isAuthenticated()) {
            throw new HttpException('No autenticado', 401);
        }

        $user = $this->sessionService->getUser();
        $tipoUsuario = strtolower((string) ($user['tipo_usuario']['nombre'] ?? ''));

        if ($tipoUsuario !== 'ceo') {
            throw new HttpException('Solo el CEO puede realizar esta accion.', 403);
        }
    }
}
