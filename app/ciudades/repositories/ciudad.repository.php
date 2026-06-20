<?php

namespace App\Ciudades\Repositories;

use App\Ciudades\Models\Ciudad;
use PDO;

require_once __DIR__ . '/../models/ciudad.model.php';

class CiudadRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @return Ciudad[]
     */
    public function getTodas(): array
    {
        $stmt = $this->pdo->query($this->selectBase() . ' ORDER BY c.nombre ASC');

        return array_map(
            fn (array $row) => $this->mapRow($row),
            $stmt->fetchAll(PDO::FETCH_ASSOC)
        );
    }

    public function findById(int $id): ?Ciudad
    {
        $stmt = $this->pdo->prepare($this->selectBase() . ' WHERE c.id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->mapRow($row) : null;
    }

    private function selectBase(): string
    {
        return '
            SELECT
                c.id,
                c.nombre,
                c.abreviacion,
                c.pais_id,
                p.nombre AS pais_nombre
            FROM ciudades c
            INNER JOIN paises p ON p.id = c.pais_id
        ';
    }

    private function mapRow(array $row): Ciudad
    {
        return new Ciudad(
            id: (int) $row['id'],
            nombre: (string) $row['nombre'],
            abreviacion: (string) $row['abreviacion'],
            paisId: (int) $row['pais_id'],
            nombrePais: (string) $row['pais_nombre']
        );
    }
}
