<?php

namespace App\Reservas\Validators;

use App\Shared\Http\HttpException;

require_once __DIR__ . '/../../shared/http/http-exception.php';

class ReservaValidator
{
    public static function validatePaymentForm(array $data): void
    {
        if (!isset($data['pago']) || !is_array($data['pago'])) {
            throw new HttpException('El metodo de pago es obligatorio.', 400, ['field' => 'pago']);
        }

        $pago = $data['pago'];
        self::requiredString($pago, 'nombreTitular', 120, 'pago.nombreTitular');

        $numeroTarjeta = preg_replace('/\s+/', '', self::requiredString($pago, 'numeroTarjeta', 23, 'pago.numeroTarjeta'));
        if (!is_string($numeroTarjeta) || preg_match('/^\d{13,19}$/', $numeroTarjeta) !== 1) {
            throw new HttpException('El numero de tarjeta no tiene un formato valido.', 400, ['field' => 'pago.numeroTarjeta']);
        }

        $vencimiento = self::requiredString($pago, 'vencimiento', 5, 'pago.vencimiento');
        if (preg_match('/^(0[1-9]|1[0-2])\/(\d{2})$/', $vencimiento) !== 1) {
            throw new HttpException('El vencimiento debe tener el formato MM/AA.', 400, ['field' => 'pago.vencimiento']);
        }

        $cvv = self::requiredString($pago, 'cvv', 4, 'pago.cvv');
        if (preg_match('/^\d{3,4}$/', $cvv) !== 1) {
            throw new HttpException('El codigo de seguridad no tiene un formato valido.', 400, ['field' => 'pago.cvv']);
        }

        if (($pago['aceptaTerminos'] ?? null) !== '1') {
            throw new HttpException('Debes aceptar los terminos y condiciones.', 400, ['field' => 'pago.aceptaTerminos']);
        }
    }

    public static function validate(array $data): void
    {
        self::positiveInteger($data, 'vueloId', 'El vuelo es obligatorio.');

        if (!isset($data['pasajeros']) || !is_array($data['pasajeros']) || $data['pasajeros'] === []) {
            throw new HttpException('Debes incluir al menos un pasajero.', 400, ['field' => 'pasajeros']);
        }

        foreach ($data['pasajeros'] as $index => $pasajero) {
            if (!is_array($pasajero)) {
                throw new HttpException('Los datos de los pasajeros no tienen un formato valido.', 400, ['field' => "pasajeros.$index"]);
            }

            self::requiredString($pasajero, 'nombre', 80, "pasajeros.$index.nombre");
            self::requiredString($pasajero, 'apellido', 80, "pasajeros.$index.apellido");
            self::requiredString($pasajero, 'documento', 30, "pasajeros.$index.documento");
            self::requiredString($pasajero, 'pasaporte', 30, "pasajeros.$index.pasaporte");
            self::requiredString($pasajero, 'telefonoContacto', 30, "pasajeros.$index.telefonoContacto");
            $email = self::requiredString($pasajero, 'correoElectronico', 120, "pasajeros.$index.correoElectronico");

            if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                throw new HttpException('El correo del pasajero no tiene un formato valido.', 400, ['field' => "pasajeros.$index.correoElectronico"]);
            }

            self::date($pasajero, 'fechaNacimiento', "pasajeros.$index.fechaNacimiento");
        }

        if (!isset($data['pago']) || !is_array($data['pago'])) {
            throw new HttpException('El metodo de pago es obligatorio.', 400, ['field' => 'pago']);
        }

        $pago = $data['pago'];
        self::requiredString($pago, 'nombreTitular', 120, 'pago.nombreTitular');
        $numeroTarjeta = self::requiredString($pago, 'numeroTarjeta', 19, 'pago.numeroTarjeta');

        if (preg_match('/^\d{13,19}$/', $numeroTarjeta) !== 1) {
            throw new HttpException('El numero de tarjeta no tiene un formato valido.', 400, ['field' => 'pago.numeroTarjeta']);
        }

        $mes = self::positiveInteger($pago, 'vencimientoMes', 'El mes de vencimiento es obligatorio.', 'pago.vencimientoMes');
        if ($mes > 12) {
            throw new HttpException('El mes de vencimiento no es valido.', 400, ['field' => 'pago.vencimientoMes']);
        }

        $anio = self::positiveInteger($pago, 'vencimientoAnio', 'El anio de vencimiento es obligatorio.', 'pago.vencimientoAnio');
        if ($anio < 1000 || $anio > 9999) {
            throw new HttpException('El anio de vencimiento debe tener cuatro digitos.', 400, ['field' => 'pago.vencimientoAnio']);
        }
    }

    private static function requiredString(array $data, string $key, int $maxLength, string $field): string
    {
        if (!array_key_exists($key, $data) || !is_scalar($data[$key])) {
            throw new HttpException('Este campo es obligatorio.', 400, ['field' => $field]);
        }

        $value = trim((string) $data[$key]);
        if ($value === '') {
            throw new HttpException('Este campo es obligatorio.', 400, ['field' => $field]);
        }

        if (strlen($value) > $maxLength) {
            throw new HttpException("Este campo no puede superar los $maxLength caracteres.", 400, ['field' => $field]);
        }

        return $value;
    }

    private static function positiveInteger(
        array $data,
        string $key,
        string $message,
        ?string $field = null
    ): int {
        $value = $data[$key] ?? null;
        $validated = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

        if ($validated === false) {
            throw new HttpException($message, 400, ['field' => $field ?? $key]);
        }

        return (int) $validated;
    }

    private static function date(array $data, string $key, string $field): void
    {
        $value = self::requiredString($data, $key, 10, $field);
        $date = \DateTime::createFromFormat('!Y-m-d', $value);
        $errors = \DateTime::getLastErrors();

        if ($date === false || ($errors !== false && ($errors['warning_count'] > 0 || $errors['error_count'] > 0)) || $date->format('Y-m-d') !== $value) {
            throw new HttpException('La fecha no tiene un formato valido.', 400, ['field' => $field]);
        }
    }
}
