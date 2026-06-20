<?php

namespace App\Auth\Services;

use App\Auth\Models\Usuario;
use App\Shared\Http\HttpException;
use App\Shared\Services\EmailService;
use Throwable;

require_once __DIR__ . '/../models/usuario.model.php';
require_once __DIR__ . '/../../shared/http/http-exception.php';
require_once __DIR__ . '/../../shared/services/email.service.php';

class ConfirmacionUsuarioEmailService
{
    private EmailService $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function send(Usuario $usuario): void
    {
        if (!$usuario->tokenVerificacion) {
            throw new HttpException('El usuario no tiene token de confirmacion.', 500);
        }

        $confirmationUrl = $this->buildConfirmationUrl($usuario->tokenVerificacion);
        $fullName = trim($usuario->nombre . ' ' . $usuario->apellido);

        try {
            $this->emailService->send(
                $usuario->email,
                $fullName,
                'Confirma tu cuenta de Flyto',
                $this->htmlBody($usuario->nombre, $confirmationUrl),
                $this->textBody($confirmationUrl)
            );
        } catch (Throwable $exception) {
            throw new HttpException('No se pudo enviar el email de confirmacion.', 500);
        }
    }

    private function buildConfirmationUrl(string $token): string
    {
        $appUrl = getenv('APP_URL');

        if ($appUrl === false || $appUrl === '') {
            throw new HttpException('Falta configurar APP_URL.', 500);
        }

        return rtrim($appUrl, '/') . '/auth/confirmar?token=' . rawurlencode($token);
    }

    private function htmlBody(string $nombre, string $confirmationUrl): string
    {
        $safeName = htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8');
        $safeUrl = htmlspecialchars($confirmationUrl, ENT_QUOTES, 'UTF-8');

        return "
            <h1>Confirma tu cuenta</h1>
            <p>Hola {$safeName}, gracias por registrarte en Flyto.</p>
            <p>Para activar tu cuenta, ingresa al siguiente enlace:</p>
            <p><a href=\"{$safeUrl}\">Confirmar cuenta</a></p>
        ";
    }

    private function textBody(string $confirmationUrl): string
    {
        return "Confirma tu cuenta ingresando a este enlace: {$confirmationUrl}";
    }
}
