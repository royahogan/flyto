<?php

namespace App\Vuelos\Repositories;

use App\Vuelos\Dtos\BuscarVuelosDto;
use App\Vuelos\Models\Vuelo;
use PDO;

require_once __DIR__ . '/../dtos/buscar-vuelos.dto.php';
require_once __DIR__ . '/../models/vuelo.model.php';

class VueloRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @return Vuelo[]
     */
    public function buscarDisponibles(BuscarVuelosDto $dto): array
    {
        $sql = "
            SELECT
                v.id,
                v.codigoVuelo,
                v.aerolinea_id,
                a.nombre AS aerolinea_nombre,
                a.codigo_iata AS aerolinea_codigo_iata,
                promocion.id AS promocion_id,
                promocion.descripcion AS promocion_descripcion,
                promocion.descuento AS promocion_descuento,
                promocion.fecha_creacion AS promocion_fecha_creacion,
                promocion.fecha_aprobacion AS promocion_fecha_aprobacion,
                promocion.fecha_fin AS promocion_fecha_fin,
                promocion.estado AS promocion_estado,
                v.origen_ciudad_id,
                origen.nombre AS origen_nombre,
                origen.abreviacion AS origen_abreviacion,
                origen_pais.id AS origen_pais_id,
                origen_pais.nombre AS origen_pais_nombre,
                v.destino_ciudad_id,
                destino.nombre AS destino_nombre,
                destino.abreviacion AS destino_abreviacion,
                destino_pais.id AS destino_pais_id,
                destino_pais.nombre AS destino_pais_nombre,
                v.precio,
                v.asientos_disponibles,
                v.fecha_salida,
                v.fecha_llegada,
                v.fecha_creacion,
                v.distancia_km,
                v.duracion_horas,
                ev.nombre AS estado,
                v.asientosOcupados AS asientos_ocupados
            FROM vuelos v
            INNER JOIN aerolineas a ON a.id = v.aerolinea_id
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
                WHERE LOWER(ep.nombre) = 'activa'
            ) promocion ON promocion.aerolinea_id = a.id
            INNER JOIN ciudades origen ON origen.id = v.origen_ciudad_id
            INNER JOIN paises origen_pais ON origen_pais.id = origen.pais_id
            INNER JOIN ciudades destino ON destino.id = v.destino_ciudad_id
            INNER JOIN paises destino_pais ON destino_pais.id = destino.pais_id
            INNER JOIN estados_vuelos ev ON ev.id = v.estado_id
            WHERE v.origen_ciudad_id = :origen
                AND v.destino_ciudad_id = :destino
                AND DATE(v.fecha_salida) = :fechaSalida
                AND LOWER(ev.nombre) = 'pendiente'
            GROUP BY
                v.id,
                v.codigoVuelo,
                v.aerolinea_id,
                a.nombre,
                a.codigo_iata,
                promocion.id,
                promocion.descripcion,
                promocion.descuento,
                promocion.fecha_creacion,
                promocion.fecha_aprobacion,
                promocion.fecha_fin,
                promocion.estado,
                v.origen_ciudad_id,
                origen.nombre,
                origen.abreviacion,
                origen_pais.id,
                origen_pais.nombre,
                v.destino_ciudad_id,
                destino.nombre,
                destino.abreviacion,
                destino_pais.id,
                destino_pais.nombre,
                v.precio,
                v.asientos_disponibles,
                v.fecha_salida,
                v.fecha_llegada,
                v.fecha_creacion,
                v.distancia_km,
                v.duracion_horas,
                ev.nombre
            HAVING (v.asientos_disponibles - asientos_ocupados) >= :cantidadPasajeros
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':origen' => $dto->origen,
            ':destino' => $dto->destino,
            ':fechaSalida' => $dto->fechaSalida,
            ':cantidadPasajeros' => $dto->cantidadPasajeros,
        ]);

        return array_map(fn (array $row) => $this->mapRow($row), $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    private function mapRow(array $row): Vuelo
    {
        $origenId = (int) $row['origen_ciudad_id'];
        $destinoId = (int) $row['destino_ciudad_id'];
        $codigoIata = strtoupper((string) $row['aerolinea_codigo_iata']);

        return new Vuelo(
            id: (int) $row['id'],
            codigoVuelo: (string) $row['codigoVuelo'],
            fechaSalida: new \DateTime($row['fecha_salida']),
            fechaLlegada: new \DateTime($row['fecha_llegada']),
            fechaCreacion: new \DateTime($row['fecha_creacion']),
            precio: (float) $row['precio'],
            distancia: (int) $row['distancia_km'],
            duracion: (float) $row['duracion_horas'],
            estado: (string) $row['estado'],
            asientosDisponibles: (int) $row['asientos_disponibles'],
            asientosOcupados: (int) $row['asientos_ocupados'],
            ciudadOrigen: [
                'idCiudad' => $origenId,
                'nombreCiudad' => (string) $row['origen_nombre'],
                'abreviacionCiudad' => (string) $row['origen_abreviacion'],
                'pais' => [
                    'idPais' => (int) $row['origen_pais_id'],
                    'nombrePais' => (string) $row['origen_pais_nombre'],
                ],
            ],
            ciudadDestino: [
                'idCiudad' => $destinoId,
                'nombreCiudad' => (string) $row['destino_nombre'],
                'abreviacionCiudad' => (string) $row['destino_abreviacion'],
                'pais' => [
                    'idPais' => (int) $row['destino_pais_id'],
                    'nombrePais' => (string) $row['destino_pais_nombre'],
                ],
            ],
            aerolinea: [
                'idAerolinea' => (int) $row['aerolinea_id'],
                'codigoIataAerolinea' => $codigoIata,
                'nombreAerolinea' => (string) $row['aerolinea_nombre'],
            ],
            promocion: $row['promocion_id'] === null ? null : [
                'idPromocion' => (int) $row['promocion_id'],
                'descripcion' => (string) $row['promocion_descripcion'],
                'descuento' => (float) $row['promocion_descuento'],
                'fechaCreacion' => (string) $row['promocion_fecha_creacion'],
                'fechaAprobacion' => $row['promocion_fecha_aprobacion'],
                'fechaFin' => $row['promocion_fecha_fin'],
                'estado' => (string) $row['promocion_estado'],
            ]
        );
    }
}
