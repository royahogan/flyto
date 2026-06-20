<?php

namespace App\Auth\Controllers;

use App\Auth\Dtos\RegisterUsuarioDTO;
use App\Auth\Services\RegisterUsuarioService;
use App\Auth\Services\SessionService;
use App\Auth\Validators\RegisterUsuarioValidator;
use App\Shared\Http\Flash;
use App\Shared\Http\HttpException;
use App\Shared\Http\RedirectResponse;
use Throwable;

require_once __DIR__ . '/../dtos/register-usuario.dto.php';
require_once __DIR__ . '/../services/register-usuario.service.php';
require_once __DIR__ . '/../services/session.service.php';
require_once __DIR__ . '/../validators/register-usuario.validator.php';
require_once __DIR__ . '/../../shared/http/flash.php';
require_once __DIR__ . '/../../shared/http/http-exception.php';
require_once __DIR__ . '/../../shared/http/redirect-response.php';

class RegisterUsuarioActionController
{
    private RegisterUsuarioService $registerUsuarioService;
    private SessionService $sessionService;

    public function __construct(
        RegisterUsuarioService $registerUsuarioService,
        SessionService $sessionService
    ) {
        $this->registerUsuarioService = $registerUsuarioService;
        $this->sessionService = $sessionService;
    }

    public function register(array $params = [], array $query = []): void
    {
        $this->sessionService->start();

        if ($this->sessionService->isAuthenticated()) {
            RedirectResponse::to('/', [], 303);
            return;
        }

        try {
            $data = $_POST;

            RegisterUsuarioValidator::validate($data);

            $dto = new RegisterUsuarioDTO(
                $data['email'],
                $data['password'],
                $data['nombre'],
                $data['apellido'],
                $data['telefono'] ?? null
            );

            $this->registerUsuarioService->execute($dto);

            Flash::success('Revisa tu email para confirmar la cuenta.');
            RedirectResponse::to('/auth/registro/confirmacion-enviada', [], 303);
        } catch (HttpException $exception) {
            Flash::error('No pudimos crear la cuenta. Revisa los datos e intentalo nuevamente.');
            Flash::validationErrors($this->validationErrorsFromException($exception));
            Flash::old($this->safeOldInput($_POST));

            RedirectResponse::to('/auth/registro', [], 303);
        } catch (Throwable) {
            Flash::error('No pudimos crear la cuenta. Intentalo nuevamente en unos minutos.');
            Flash::old($this->safeOldInput($_POST));

            RedirectResponse::to('/auth/registro', [], 303);
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
