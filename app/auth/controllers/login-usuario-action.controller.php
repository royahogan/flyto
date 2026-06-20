<?php

namespace App\Auth\Controllers;

use App\Auth\Dtos\LoginUsuarioDTO;
use App\Auth\Services\LoginUsuarioService;
use App\Auth\Services\SessionService;
use App\Auth\Validators\LoginUsuarioValidator;
use App\Shared\Http\Flash;
use App\Shared\Http\HttpException;
use App\Shared\Http\RedirectResponse;
use Throwable;

require_once __DIR__ . '/../dtos/login-usuario.dto.php';
require_once __DIR__ . '/../services/login-usuario.service.php';
require_once __DIR__ . '/../services/session.service.php';
require_once __DIR__ . '/../validators/login-usuario.validator.php';
require_once __DIR__ . '/../../shared/http/flash.php';
require_once __DIR__ . '/../../shared/http/http-exception.php';
require_once __DIR__ . '/../../shared/http/redirect-response.php';

class LoginUsuarioActionController
{
    private LoginUsuarioService $loginUsuarioService;
    private SessionService $sessionService;

    public function __construct(
        LoginUsuarioService $loginUsuarioService,
        SessionService $sessionService
    ) {
        $this->loginUsuarioService = $loginUsuarioService;
        $this->sessionService = $sessionService;
    }

    public function login(array $params = [], array $query = []): void
    {
        $this->sessionService->start();

        if ($this->sessionService->isAuthenticated()) {
            RedirectResponse::to('/', [], 303);
            return;
        }

        try {
            $data = $_POST;

            LoginUsuarioValidator::validate($data);

            $dto = new LoginUsuarioDTO(
                $data['email'],
                $data['password']
            );

            $this->loginUsuarioService->execute($dto);

            RedirectResponse::to('/', [], 303);
        } catch (HttpException $exception) {
            Flash::error('No pudimos iniciar sesion. Revisa los datos e intentalo nuevamente.');
            Flash::validationErrors($this->validationErrorsFromException($exception));
            Flash::old($this->safeOldInput($_POST));

            RedirectResponse::to('/auth/login', [], 303);
        } catch (Throwable) {
            Flash::error('No pudimos iniciar sesion. Intentalo nuevamente en unos minutos.');
            Flash::old($this->safeOldInput($_POST));

            RedirectResponse::to('/auth/login', [], 303);
        }
    }

    private function validationErrorsFromException(HttpException $exception): array
    {
        $details = $exception->getDetails();
        $field = $details['field'] ?? null;

        if (!is_string($field) || $field === '') {
            return ['general' => $exception->getMessage()];
        }

        return [$field => $exception->getMessage()];
    }

    private function safeOldInput(array $input): array
    {
        unset($input['password'], $input['password_confirmation']);

        return $input;
    }
}
