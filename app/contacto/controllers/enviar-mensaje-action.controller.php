<?php

namespace App\Contacto\Controllers;

use App\Contacto\Services\EnviarMensajeService;
use App\Shared\Http\Flash;
use App\Shared\Http\HttpException;
use App\Shared\Http\RedirectResponse;
use Throwable;

require_once __DIR__ . '/../services/enviar-mensaje.service.php';
require_once __DIR__ . '/../../shared/http/flash.php';
require_once __DIR__ . '/../../shared/http/http-exception.php';
require_once __DIR__ . '/../../shared/http/redirect-response.php';

class EnviarMensajeActionController
{
    private EnviarMensajeService $enviarMensajeService;

    public function __construct(EnviarMensajeService $enviarMensajeService)
    {
        $this->enviarMensajeService = $enviarMensajeService;
    }

    public function enviar(array $params = [], array $query = []): void
    {
        $redirectTo = $_POST['redirectTo'] ?? 'contacto';
        $target = $redirectTo === 'home' ? '/' : '/contacto';
        $oldInput = $_POST;
        unset($oldInput['redirectTo']);

        try {
            $this->enviarMensajeService->execute($_POST);

            Flash::success('Tu consulta fue enviada correctamente.');
            RedirectResponse::to($target, [], 303, 'contacto');
        } catch (HttpException $exception) {
            Flash::error('No pudimos enviar tu consulta. Revisá los datos e intentalo nuevamente.');
            Flash::validationErrors($this->validationErrorsFromException($exception));
            Flash::old($oldInput);

            RedirectResponse::to($target, [], 303, 'contacto');
        } catch (Throwable) {
            Flash::error('No pudimos enviar tu consulta. Intentalo nuevamente en unos minutos.');
            Flash::old($oldInput);

            RedirectResponse::to($target, [], 303, 'contacto');
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
}
