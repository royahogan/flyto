<?php

namespace App\Reservas\Repositories;

use App\Reservas\Models\MetodoPago;
use App\Reservas\Models\Pasajero;
use App\Reservas\Models\Reserva;
use App\Shared\Http\HttpException;
use App\Vuelos\Models\Vuelo;
use PDO;
use Throwable;

require_once __DIR__ . '/../models/reserva.model.php';
require_once __DIR__ . '/../../shared/http/http-exception.php';

class ReservaRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /** @return Reserva[] */
    public function getTodas(): array
    {
        return $this->fetchReservas();
    }

    /** @return Reserva[] */
    public function getPorUsuario(int $usuarioId): array
    {
        return $this->fetchReservas('r.usuario_id = :usuario_id', [':usuario_id' => $usuarioId]);
    }

    /** @return Reserva[] */
    public function getPorEstado(string $estado): array
    {
        return $this->fetchReservas('LOWER(er.nombre) = :estado', [':estado' => strtolower($estado)]);
    }

    public function findById(int $id): ?Reserva
    {
        $reservas = $this->fetchReservas('r.id = :id', [':id' => $id]);

        return $reservas[0] ?? null;
    }

    public function findVueloParaReserva(int $vueloId): ?Vuelo
    {
        $sql = "
            SELECT
                v.id,
                v.codigoVuelo,
                v.fecha_salida,
                v.fecha_llegada,
                v.fecha_creacion,
                v.precio,
                v.distancia_km,
                v.duracion_horas,
                v.asientos_disponibles,
                v.asientosOcupados AS asientos_ocupados,
                ev.nombre AS vuelo_estado,
                a.id AS aerolinea_id,
                a.nombre AS aerolinea_nombre,
                a.codigo_iata AS aerolinea_codigo_iata,
                origen.id AS origen_id,
                origen.nombre AS origen_nombre,
                origen.abreviacion AS origen_abreviacion,
                origen_pais.id AS origen_pais_id,
                origen_pais.nombre AS origen_pais_nombre,
                origen_pais.codigo AS origen_pais_codigo,
                destino.id AS destino_id,
                destino.nombre AS destino_nombre,
                destino.abreviacion AS destino_abreviacion,
                destino_pais.id AS destino_pais_id,
                destino_pais.nombre AS destino_pais_nombre,
                destino_pais.codigo AS destino_pais_codigo,
                promocion.id AS promocion_id,
                promocion.descripcion AS promocion_descripcion,
                promocion.descuento AS promocion_descuento,
                promocion.fecha_creacion AS promocion_fecha_creacion,
                promocion.fecha_aprobacion AS promocion_fecha_aprobacion,
                promocion.fecha_fin AS promocion_fecha_fin,
                promocion.estado AS promocion_estado
            FROM vuelos v
            INNER JOIN estados_vuelos ev ON ev.id = v.estado_id
            INNER JOIN aerolineas a ON a.id = v.aerolinea_id
            INNER JOIN ciudades origen ON origen.id = v.origen_ciudad_id
            INNER JOIN paises origen_pais ON origen_pais.id = origen.pais_id
            INNER JOIN ciudades destino ON destino.id = v.destino_ciudad_id
            INNER JOIN paises destino_pais ON destino_pais.id = destino.pais_id
            LEFT JOIN (
                SELECT
                    p.id,
                    p.descripcion,
                    p.descuento,
                    p.fecha_creacion,
                    p.fecha_aprobacion,
                    p.fecha_fin,
                    p.aerolinea_id,
                    ep.nombre AS estado
                FROM promociones p
                INNER JOIN estados_promociones ep ON ep.id = p.estado_id
                WHERE p.activa = TRUE
                    AND LOWER(ep.nombre) = 'activa'
                    AND p.fecha_aprobacion IS NOT NULL
                    AND p.fecha_aprobacion <= NOW()
                    AND (p.fecha_fin IS NULL OR p.fecha_fin >= NOW())
            ) promocion ON promocion.aerolinea_id = a.id
            WHERE v.id = :id
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $vueloId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->mapVuelo($row) : null;
    }

    public function create(Reserva $reserva): void
    {
        $startedTransaction = !$this->pdo->inTransaction();

        try {
            if ($startedTransaction) {
                $this->pdo->beginTransaction();
            }

            $cantidadPasajeros = count($reserva->pasajeros);
            $lock = $this->pdo->prepare("
                SELECT
                    v.asientos_disponibles,
                    v.asientosOcupados,
                    v.fecha_salida,
                    ev.nombre AS estado
                FROM vuelos v
                INNER JOIN estados_vuelos ev ON ev.id = v.estado_id
                WHERE v.id = :id
                FOR UPDATE
            ");
            $lock->execute([':id' => $reserva->vuelo->id]);
            $vuelo = $lock->fetch(PDO::FETCH_ASSOC);

            if (!$vuelo) {
                throw new HttpException('El vuelo seleccionado no existe.', 404);
            }

            if (strtolower((string) $vuelo['estado']) !== 'pendiente' || new \DateTime($vuelo['fecha_salida']) <= new \DateTime()) {
                throw new HttpException('El vuelo seleccionado ya no esta disponible.', 409);
            }

            $asientosLibres = (int) $vuelo['asientos_disponibles'] - (int) $vuelo['asientosOcupados'];
            if ($asientosLibres < $cantidadPasajeros) {
                throw new HttpException('No hay suficientes asientos disponibles.', 409);
            }

            $estadoId = $this->estadoId($reserva->estado);
            $stmt = $this->pdo->prepare("
                INSERT INTO reservas (usuario_id, vuelo_id, precio_total, fecha_reserva, estado_id)
                VALUES (:usuario_id, :vuelo_id, :precio_total, :fecha_reserva, :estado_id)
            ");
            $stmt->execute([
                ':usuario_id' => (int) $reserva->usuario['id'],
                ':vuelo_id' => $reserva->vuelo->id,
                ':precio_total' => $reserva->precioTotal,
                ':fecha_reserva' => $reserva->fechaReserva->format('Y-m-d H:i:s'),
                ':estado_id' => $estadoId,
            ]);
            $reserva->id = (int) $this->pdo->lastInsertId();

            $pasajeroStmt = $this->pdo->prepare("
                INSERT INTO pasajeros (
                    reserva_id, nombre, apellido, documento, pasaporte,
                    fecha_nacimiento, telefono_contacto, correo_electronico
                ) VALUES (
                    :reserva_id, :nombre, :apellido, :documento, :pasaporte,
                    :fecha_nacimiento, :telefono_contacto, :correo_electronico
                )
            ");

            foreach ($reserva->pasajeros as $pasajero) {
                $pasajeroStmt->execute([
                    ':reserva_id' => $reserva->id,
                    ':nombre' => $pasajero->nombre,
                    ':apellido' => $pasajero->apellido,
                    ':documento' => $pasajero->documento,
                    ':pasaporte' => $pasajero->pasaporte,
                    ':fecha_nacimiento' => $pasajero->fechaNacimiento->format('Y-m-d'),
                    ':telefono_contacto' => $pasajero->telefonoContacto,
                    ':correo_electronico' => $pasajero->correoElectronico,
                ]);
                $pasajero->id = (int) $this->pdo->lastInsertId();
            }

            $pago = $reserva->metodoPago;
            $pagoStmt = $this->pdo->prepare("
                INSERT INTO metodos_pago (
                    reserva_id, nombre_titular, ultimos_cuatro_digitos,
                    vencimiento_mes, vencimiento_anio, fecha_pago
                ) VALUES (
                    :reserva_id, :nombre_titular, :ultimos_cuatro_digitos,
                    :vencimiento_mes, :vencimiento_anio, :fecha_pago
                )
            ");
            $pagoStmt->execute([
                ':reserva_id' => $reserva->id,
                ':nombre_titular' => $pago->nombreTitular,
                ':ultimos_cuatro_digitos' => $pago->ultimosCuatroDigitos,
                ':vencimiento_mes' => $pago->vencimientoMes,
                ':vencimiento_anio' => $pago->vencimientoAnio,
                ':fecha_pago' => $pago->fechaPago->format('Y-m-d H:i:s'),
            ]);
            $pago->id = (int) $this->pdo->lastInsertId();

            $updateVuelo = $this->pdo->prepare("
                UPDATE vuelos
                SET asientosOcupados = asientosOcupados + :cantidad
                WHERE id = :id
                    AND (asientos_disponibles - asientosOcupados) >= :cantidad
            ");
            $updateVuelo->execute([':cantidad' => $cantidadPasajeros, ':id' => $reserva->vuelo->id]);

            if ($updateVuelo->rowCount() !== 1) {
                throw new HttpException('No hay suficientes asientos disponibles.', 409);
            }

            if ($startedTransaction) {
                $this->pdo->commit();
            }
        } catch (Throwable $exception) {
            if ($startedTransaction && $this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $exception;
        }
    }

    public function update(Reserva $reserva): void
    {
        if ($reserva->id === null) {
            throw new HttpException('La reserva no tiene un identificador valido.', 400);
        }

        $startedTransaction = !$this->pdo->inTransaction();

        try {
            if ($startedTransaction) {
                $this->pdo->beginTransaction();
            }

            $lock = $this->pdo->prepare("
                SELECT
                    r.vuelo_id,
                    er.nombre AS estado,
                    v.fecha_salida,
                    (SELECT COUNT(*) FROM pasajeros p WHERE p.reserva_id = r.id) AS cantidad_pasajeros
                FROM reservas r
                INNER JOIN estados_reservas er ON er.id = r.estado_id
                INNER JOIN vuelos v ON v.id = r.vuelo_id
                WHERE r.id = :id
                FOR UPDATE
            ");
            $lock->execute([':id' => $reserva->id]);
            $actual = $lock->fetch(PDO::FETCH_ASSOC);

            if (!$actual) {
                throw new HttpException('Reserva no encontrada.', 404);
            }

            $estadoActual = strtolower((string) $actual['estado']);
            $estadoNuevo = strtolower($reserva->estado);

            if ($estadoNuevo === 'cancelada') {
                if ($estadoActual !== 'confirmada') {
                    throw new HttpException('Esta reserva ya no puede cancelarse.', 409);
                }

                $limiteCancelacion = (new \DateTime())->modify('+72 hours');
                if (new \DateTime($actual['fecha_salida']) < $limiteCancelacion) {
                    throw new HttpException('La reserva solo puede cancelarse hasta 72 horas antes del vuelo.', 409);
                }

                $liberar = $this->pdo->prepare("
                    UPDATE vuelos
                    SET asientosOcupados = GREATEST(0, asientosOcupados - :cantidad)
                    WHERE id = :id
                ");
                $liberar->execute([
                    ':cantidad' => (int) $actual['cantidad_pasajeros'],
                    ':id' => (int) $actual['vuelo_id'],
                ]);
            }

            $stmt = $this->pdo->prepare('UPDATE reservas SET estado_id = :estado_id WHERE id = :id');
            $stmt->execute([
                ':estado_id' => $this->estadoId($reserva->estado),
                ':id' => $reserva->id,
            ]);

            if ($startedTransaction) {
                $this->pdo->commit();
            }
        } catch (Throwable $exception) {
            if ($startedTransaction && $this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $exception;
        }
    }

    /** @return Reserva[] */
    private function fetchReservas(string $where = '', array $params = []): array
    {
        $sql = "
            SELECT
                r.id AS reserva_id,
                r.precio_total,
                r.fecha_reserva,
                er.nombre AS reserva_estado,
                u.id AS usuario_id,
                u.email AS usuario_email,
                u.nombre AS usuario_nombre,
                u.apellido AS usuario_apellido,
                v.id,
                v.codigoVuelo,
                v.fecha_salida,
                v.fecha_llegada,
                v.fecha_creacion,
                v.precio,
                v.distancia_km,
                v.duracion_horas,
                v.asientos_disponibles,
                v.asientosOcupados AS asientos_ocupados,
                ev.nombre AS vuelo_estado,
                a.id AS aerolinea_id,
                a.nombre AS aerolinea_nombre,
                a.codigo_iata AS aerolinea_codigo_iata,
                origen.id AS origen_id,
                origen.nombre AS origen_nombre,
                origen.abreviacion AS origen_abreviacion,
                origen_pais.id AS origen_pais_id,
                origen_pais.nombre AS origen_pais_nombre,
                origen_pais.codigo AS origen_pais_codigo,
                destino.id AS destino_id,
                destino.nombre AS destino_nombre,
                destino.abreviacion AS destino_abreviacion,
                destino_pais.id AS destino_pais_id,
                destino_pais.nombre AS destino_pais_nombre,
                destino_pais.codigo AS destino_pais_codigo,
                mp.id AS pago_id,
                mp.nombre_titular,
                mp.ultimos_cuatro_digitos,
                mp.vencimiento_mes,
                mp.vencimiento_anio,
                mp.fecha_pago
            FROM reservas r
            INNER JOIN estados_reservas er ON er.id = r.estado_id
            INNER JOIN usuarios u ON u.id = r.usuario_id
            INNER JOIN vuelos v ON v.id = r.vuelo_id
            INNER JOIN estados_vuelos ev ON ev.id = v.estado_id
            INNER JOIN aerolineas a ON a.id = v.aerolinea_id
            INNER JOIN ciudades origen ON origen.id = v.origen_ciudad_id
            INNER JOIN paises origen_pais ON origen_pais.id = origen.pais_id
            INNER JOIN ciudades destino ON destino.id = v.destino_ciudad_id
            INNER JOIN paises destino_pais ON destino_pais.id = destino.pais_id
            INNER JOIN metodos_pago mp ON mp.reserva_id = r.id
        ";

        if ($where !== '') {
            $sql .= " WHERE $where";
        }

        $sql .= ' ORDER BY r.fecha_reserva DESC, r.id DESC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return array_map(fn (array $row) => $this->mapReserva($row), $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    private function mapReserva(array $row): Reserva
    {
        $pasajerosStmt = $this->pdo->prepare("
            SELECT id, nombre, apellido, documento, pasaporte, fecha_nacimiento,
                   telefono_contacto, correo_electronico
            FROM pasajeros
            WHERE reserva_id = :reserva_id
            ORDER BY id
        ");
        $pasajerosStmt->execute([':reserva_id' => (int) $row['reserva_id']]);

        $pasajeros = array_map(
            fn (array $pasajero) => new Pasajero(
                id: (int) $pasajero['id'],
                nombre: (string) $pasajero['nombre'],
                apellido: (string) $pasajero['apellido'],
                documento: (string) $pasajero['documento'],
                pasaporte: (string) $pasajero['pasaporte'],
                fechaNacimiento: new \DateTime($pasajero['fecha_nacimiento']),
                telefonoContacto: (string) $pasajero['telefono_contacto'],
                correoElectronico: (string) $pasajero['correo_electronico']
            ),
            $pasajerosStmt->fetchAll(PDO::FETCH_ASSOC)
        );

        return new Reserva(
            id: (int) $row['reserva_id'],
            usuario: [
                'id' => (int) $row['usuario_id'],
                'email' => (string) $row['usuario_email'],
                'nombre' => (string) $row['usuario_nombre'],
                'apellido' => (string) $row['usuario_apellido'],
            ],
            vuelo: $this->mapVuelo($row),
            precioTotal: (float) $row['precio_total'],
            fechaReserva: new \DateTime($row['fecha_reserva']),
            estado: (string) $row['reserva_estado'],
            pasajeros: $pasajeros,
            metodoPago: new MetodoPago(
                id: (int) $row['pago_id'],
                nombreTitular: (string) $row['nombre_titular'],
                ultimosCuatroDigitos: (string) $row['ultimos_cuatro_digitos'],
                vencimientoMes: (int) $row['vencimiento_mes'],
                vencimientoAnio: (int) $row['vencimiento_anio'],
                fechaPago: new \DateTime($row['fecha_pago'])
            )
        );
    }

    private function mapVuelo(array $row): Vuelo
    {
        $promocion = null;
        if (array_key_exists('promocion_id', $row) && $row['promocion_id'] !== null) {
            $promocion = [
                'idPromocion' => (int) $row['promocion_id'],
                'descripcion' => (string) $row['promocion_descripcion'],
                'descuento' => (float) $row['promocion_descuento'],
                'fechaCreacion' => (string) $row['promocion_fecha_creacion'],
                'fechaAprobacion' => $row['promocion_fecha_aprobacion'],
                'fechaFin' => $row['promocion_fecha_fin'],
                'estado' => (string) $row['promocion_estado'],
            ];
        }

        return new Vuelo(
            id: (int) $row['id'],
            codigoVuelo: (string) $row['codigoVuelo'],
            fechaSalida: new \DateTime($row['fecha_salida']),
            fechaLlegada: new \DateTime($row['fecha_llegada']),
            fechaCreacion: new \DateTime($row['fecha_creacion']),
            precio: (float) $row['precio'],
            distancia: (int) $row['distancia_km'],
            duracion: (float) $row['duracion_horas'],
            estado: (string) $row['vuelo_estado'],
            asientosDisponibles: (int) $row['asientos_disponibles'],
            asientosOcupados: (int) $row['asientos_ocupados'],
            ciudadOrigen: [
                'idCiudad' => (int) $row['origen_id'],
                'nombreCiudad' => (string) $row['origen_nombre'],
                'abreviacionCiudad' => (string) $row['origen_abreviacion'],
                'pais' => [
                    'idPais' => (int) $row['origen_pais_id'],
                    'nombrePais' => (string) $row['origen_pais_nombre'],
                    'codigoPais' => (string) $row['origen_pais_codigo'],
                ],
            ],
            ciudadDestino: [
                'idCiudad' => (int) $row['destino_id'],
                'nombreCiudad' => (string) $row['destino_nombre'],
                'abreviacionCiudad' => (string) $row['destino_abreviacion'],
                'pais' => [
                    'idPais' => (int) $row['destino_pais_id'],
                    'nombrePais' => (string) $row['destino_pais_nombre'],
                    'codigoPais' => (string) $row['destino_pais_codigo'],
                ],
            ],
            aerolinea: [
                'idAerolinea' => (int) $row['aerolinea_id'],
                'nombreAerolinea' => (string) $row['aerolinea_nombre'],
                'codigoIataAerolinea' => strtoupper((string) $row['aerolinea_codigo_iata']),
            ],
            promocion: $promocion
        );
    }

    private function estadoId(string $estado): int
    {
        $stmt = $this->pdo->prepare('SELECT id FROM estados_reservas WHERE LOWER(nombre) = :nombre LIMIT 1');
        $stmt->execute([':nombre' => strtolower($estado)]);
        $id = $stmt->fetchColumn();

        if ($id === false) {
            throw new HttpException('El estado de la reserva no esta configurado.', 500);
        }

        return (int) $id;
    }
}
