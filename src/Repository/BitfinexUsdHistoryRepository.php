<?php

namespace App\Repository;

use App\Entity\BitfinexUsdHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method BitfinexUsdHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method BitfinexUsdHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method BitfinexUsdHistory[]    findAll()
 * @method BitfinexUsdHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BitfinexUsdHistoryRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, BitfinexUsdHistory::class);
    }

    /**
     * @return BitfinexUsdHistory
     */
    public function findLastData(): array
    {
        // automatically knows to select BitfinexUsdHistories
        // the "mu" is an alias you'll use in the rest of the query
        $qb = $this->createQueryBuilder('mu')
            ->select('mu.spot as spot')
            ->orderBy('mu.insert_time', 'DESC')
            ->setMaxResults(1)
            ->getQuery();

        return $qb->execute();
    }

    /**
     * @return BitfinexUsdHistory[]
     */
    public function findTwoLastData(): array
    {
        // automatically knows to select BitfinexUsdHistories
        // the "mu" is an alias you'll use in the rest of the query
        $qb = $this->createQueryBuilder('mu')
            ->select('mu.spot as spot')
            ->orderBy('mu.insert_time', 'DESC')
            ->setMaxResults(2)
            ->getQuery();

        return $qb->execute();
    }

    /**
     * @param $time_value
     * @return BitfinexUsdHistory
     */
    public function findPeriodFirstSpot($time_value): array
    {
        $qb = $this->createQueryBuilder('mu')
            ->select('mu.spot as spot')
            ->andWhere('mu.insert_time >= :time_value')
            ->setParameter('time_value', $time_value)
            ->orderBy('mu.insert_time', 'ASC')
            ->setMaxResults(1)
            ->getQuery();

        return $qb->execute();
    }

    /**
     * @param $start_period
     * @param $end_period
     * @return BitfinexUsdHistory
     */
    public function findPeriodHighLowSpot($start_period, $end_period): array
    {
        $qb = $this->createQueryBuilder('mu')
            ->select('MAX(mu.spot) AS high_value, MIN(mu.spot) as low_value')
            ->andWhere('mu.insert_time >= :start_time')
            ->andWhere('mu.insert_time <= :end_time')
            ->setParameter('start_time', $start_period)
            ->setParameter('end_time', $end_period)
            ->getQuery();

        return $qb->execute();
    }

    /**
     * @param $end_period
     * @return BitfinexUsdHistory
     */
    public function findPeriodLastInsertTime($end_period): array
    {
        $qb = $this->createQueryBuilder('mu')
            ->select('MAX(mu.insert_time) AS last_time')
            ->andWhere('mu.insert_time <= :end_time')
            ->setParameter('end_time', $end_period)
            ->getQuery();

        return $qb->execute();
    }

}
