<?php

namespace App\Repository;

use App\Entity\Material;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class CoverRepository.
 */
class MaterialRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Material::class);
    }

    /**
     * @param int $agencyId
     *
     * @return array
     */
    public function getByAgencyId(int $agencyId): array
    {
        $query = $this->createQueryBuilder('m')
            ->where('m.agencyId = '.$agencyId)
            ->getQuery();

        return $query->execute();
    }
}
