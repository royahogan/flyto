<?php

namespace App\Reservas\Validators;

use App\Shared\Http\HttpException;

require_once __DIR__ . '/../../shared/http/http-exception.php';

class PasajerosValidator
{
    public static function validateQuery(array $data): void
    {
        self::integerBetween($data, 'vueloId', 1, PHP_INT_MAX, 'El vuelo seleccionado no es valido.');
        self::integerBetween($data, 'cantidadPasajeros', 1, 4, 'La cantidad de pasajeros debe ser un entero entre 1 y 4.');
    }

    public static function validate(array $data): void
    {
        self::validateQuery($data);
        $cantidad = (int) $data['cantidadPasajeros'];

        if (!isset($data['pasajeros']) || !is_array($data['pasajeros']) || count($data['pasajeros']) !== $cantidad) {
            throw new HttpException('Debes completar los datos de todos los pasajeros.', 400, ['field' => 'pasajeros']);
        }

        foreach (array_values($data['pasajeros']) as $index => $pasajero) {
            if (!is_array($pasajero)) {
                throw new HttpException('Los datos del pasajero no tienen un formato valido.', 400, ['field' => "pasajeros.$index"]);
            }

            self::requiredString($pasajero, 'nombre', 80, "pasajeros.$index.nombre");
            self::requiredString($pasajero, 'apellido', 80, "pasajeros.$index.apellido");
            self::requiredString($pasajero, 'documento', 30, "pasajeros.$index.documento");
            self::requiredString($pasajero, 'pasaporte', 30, "pasajeros.$index.pasaporte");
            self::date($pasajero, 'fechaNacimiento', "pasajeros.$index.fechaNacimiento");
            self::requiredString($pasajero, 'nacionalidad', 80, "pasajeros.$index.nacionalidad");
            $email = self::requiredString($pasajero, 'correoElectronico', 120, "pasajeros.$index.correoElectronico");
            self::requiredString($pasajero, 'telefonoContacto', 30, "pasajeros.$index.telefonoContacto");

            if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                throw new HttpException('El correo electronico no tiene un formato valido.', 400, ['field' => "pasajeros.$index.correoElectronico"]);
            }
        }
    }

    private static function integerBetween(array $data, string $key, int $min, int $max, string $message): int
    {
        $value = $data[$key] ?? null;
        $validated = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => $min, 'max_range' => $max]]);

        if ($validated === false) {
            throw new HttpException($message, 400, ['field' => $key]);
        }

        return (int) $validated;
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

    private static function date(array $data, string $key, string $field): void
    {
        $value = self::requiredString($data, $key, 10, $field);
        $date = \DateTimeImmutable::createFromFormat('!Y-m-d', $value);
        $errors = \DateTimeImmutable::getLastErrors();

        if (!$date || ($errors !== false && ($errors['warning_count'] > 0 || $errors['error_count'] > 0)) || $date->format('Y-m-d') !== $value) {
            throw new HttpException('La fecha de nacimiento no tiene un formato valido.', 400, ['field' => $field]);
        }

        if ($date >= new \DateTimeImmutable('today')) {
            throw new HttpException('La fecha de nacimiento debe ser anterior a hoy.', 400, ['field' => $field]);
        }
    }
}
