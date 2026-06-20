<?php

namespace App\Shared\Http;

use App\Auth\Services\SessionService;

require_once __DIR__ . '/../../auth/services/session.service.php';

class ViewResponse
{
    private SessionService $sessionService;

    public function __construct(SessionService $sessionService)
    {
        $this->sessionService = $sessionService;
    }

    public function render(
        string $viewPath,
        string $title,
        array $viewData,
        int $statusCode,
        string $layoutPath
    ): void {
        $this->sessionService->start();

        http_response_code($statusCode);

        $basePath = self::basePath();
        $currentPath = self::currentPath($basePath);
        $pageTitle = $title;
        $currentUser = $this->sessionService->getUser();
        $isAuthenticated = $currentUser !== null;

        ob_start();
        extract($viewData, EXTR_SKIP);
        require $viewPath;
        $content = ob_get_clean();

        require $layoutPath;
    }

    private static function basePath(): string
    {
        $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));

        return $scriptDir === '/' ? '' : rtrim($scriptDir, '/');
    }

    private static function currentPath(string $basePath): string
    {
        $requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

        if ($basePath !== '' && str_starts_with($requestPath, $basePath)) {
            $requestPath = substr($requestPath, strlen($basePath)) ?: '/';
        }

        return $requestPath !== '/' ? rtrim($requestPath, '/') : '/';
    }
}
