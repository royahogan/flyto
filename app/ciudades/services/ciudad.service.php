<?php

namespace App\Ciudades\Services;

use App\Ciudades\Models\Ciudad;
use App\Ciudades\Repositories\CiudadRepository;

require_once __DIR__ . '/../models/ciudad.model.php';
require_once __DIR__ . '/../repositories/ciudad.repository.php';

class CiudadService
{
    private CiudadRepository $ciudadRepository;

    public function __construct(CiudadRepository $ciudadRepository)
    {
        $this->ciudadRepository = $ciudadRepository;
    }

    /**
     * @return Ciudad[]
     */
    public function getTodas(): array
    {
        return $this->ciudadRepository->getTodas();
    }

    public function getPorId(int $id): ?Ciudad
    {
        return $this->ciudadRepository->findById($id);
    }
}
