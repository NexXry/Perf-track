<?php

namespace App\Repository;

use App\Entity\Tracking;
use DateInterval;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tracking>
 */
class TrackingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tracking::class);
    }

    public function findTrackingsByExerciceForCurrentWeek($exercice)
    {
        $startOfWeek = new DateTime();
        $startOfWeek->setISODate((int)$startOfWeek->format("o"), (int)$startOfWeek->format("W"), 1);
        $startOfWeek->setTime(0, 0, 0);

        $endOfWeek = clone $startOfWeek;
        $endOfWeek->add(new DateInterval('P6D'));
        $endOfWeek->setTime(23, 59, 59);

        return $this->createQueryBuilder('t')
            ->andWhere('t.Exercice = :exerciceId')
            ->andWhere('t.createdAt BETWEEN :startOfWeek AND :endOfWeek')
            ->setParameter('exerciceId', $exercice)
            ->setParameter('startOfWeek', $startOfWeek)
            ->setParameter('endOfWeek', $endOfWeek)
            ->orderBy('t.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
