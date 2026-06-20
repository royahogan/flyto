<?php

namespace App\Contacto\Controllers;

use App\Shared\Http\Flash;
use App\Shared\Http\ViewResponse;

require_once __DIR__ . '/../../shared/http/flash.php';
require_once __DIR__ . '/../../shared/http/view-response.php';

class ContactoPageController
{
    private ViewResponse $viewResponse;

    public function __construct(ViewResponse $viewResponse)
    {
        $this->viewResponse = $viewResponse;
    }

    public function show(array $params, array $query, string $layoutPath): void
    {
        $flash = Flash::consume();
        $oldInput = Flash::consumeOld();
        $validationErrors = $flash['validationErrors'] ?? [];

        $this->viewResponse->render(
            __DIR__ . '/../views/pages/contacto.page.php',
            'Contacto - Flyto',
            [
                'flash' => [
                    'success' => $flash['success'] ?? null,
                    'error' => $flash['error'] ?? null,
                ],
                'oldInput' => is_array($oldInput) ? $oldInput : [],
                'validationErrors' => is_array($validationErrors) ? $validationErrors : [],
                'contactRedirectTo' => 'contacto',
            ],
            200,
            $layoutPath
        );
    }
}
