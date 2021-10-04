<?php

namespace App\Repository;

use App\Entity\UsdHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method UsdHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method UsdHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method UsdHistory[]    findAll()
 * @method UsdHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UsdHistoryRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, UsdHistory::class);
    }

    /**
     * @param $marketName
     * @return UsdHistory
     */
    public function findLastData($marketName): array
    {
        // automatically knows to select Usdhistories
        // the "mu" is an alias you'll use in the rest of the query
        $qb = $this->createQueryBuilder('mu')
            ->select('mu.spot as spot')
            ->andWhere('mu.market = :market')
            ->setParameter('market', $marketName)
            ->orderBy('mu.insert_time', 'DESC')
            ->setMaxResults(1)
            ->getQuery();

        return $qb->execute();
    }

    /**
     * @param $marketName
     * @return UsdHistory[]
     */
    public function findTwoLastData($marketName): array
    {
        // automatically knows to select Usdhistories
        // the "mu" is an alias you'll use in the rest of the query
        $qb = $this->createQueryBuilder('mu')
            ->select('mu.spot as spot')
            ->andWhere('mu.market = :market')
            ->setParameter('market', $marketName)
            ->orderBy('mu.insert_time', 'DESC')
            ->setMaxResults(2)
            ->getQuery();

        return $qb->execute();
    }

    /**
     * @param $time_value
     * @param $market_name
     * @return UsdHistory
     */
    public function findPeriodFirstSpot($time_value, $market_name): array
    {
        $qb = $this->createQueryBuilder('mu')
            ->select('mu.spot as spot')
            ->andWhere('mu.insert_time >= :time_value')
            ->andWhere('mu.market = :market_name')
            ->setParameter('time_value', $time_value)
            ->setParameter('market_name', $market_name)
            ->orderBy('mu.insert_time', 'ASC')
            ->setMaxResults(1)
            ->getQuery();

        return $qb->execute();
    }

    /**
     * @param $start_period
     * @param $end_period
     * @param $market_name
     * @return UsdHistory
     */
    public function findPeriodHighLowSpot($start_period, $end_period, $market_name): array
    {
        $qb = $this->createQueryBuilder('mu')
            ->select('MAX(mu.spot) AS high_value, MIN(mu.spot) as low_value')
            ->andWhere('mu.insert_time >= :start_time')
            ->andWhere('mu.insert_time <= :end_time')
            ->andWhere('mu.market = :market_name')
            ->setParameter('start_time', $start_period)
            ->setParameter('end_time', $end_period)
            ->setParameter('market_name', $market_name)
            ->groupBy('mu.market')
            ->getQuery();

        return $qb->execute();
    }

    /**
     * @param $end_period
     * @param $market_name
     * @return UsdHistory
     */
    public function findPeriodLastInsertTime($end_period, $market_name): array
    {
        $qb = $this->createQueryBuilder('mu')
            ->select('MAX(mu.insert_time) AS last_time')
            ->andWhere('mu.insert_time <= :end_time')
            ->andWhere('mu.market = :market_name')
            ->setParameter('end_time', $end_period)
            ->setParameter('market_name', $market_name)
            ->groupBy('mu.market')
            ->getQuery();

        return $qb->execute();
    }

}
