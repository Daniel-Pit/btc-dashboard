<?php

namespace App\Repository;

use App\Entity\EurHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method EurHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method EurHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method EurHistory[]    findAll()
 * @method EurHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EurHistoryRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, EurHistory::class);
    }

    /**
     * @param $marketName
     * @return EurHistory
     */
    public function findLastData($marketName): array
    {
        // automatically knows to select Eurhistories
        // the "me" is an alias you'll use in the rest of the query
        $qb = $this->createQueryBuilder('me')
            ->select('me.spot as spot')
            ->andWhere('me.market = :market')
            ->setParameter('market', $marketName)
            ->orderBy('me.insert_time', 'DESC')
            ->setMaxResults(1)
            ->getQuery();

        return $qb->execute();
    }

    /**
     * @param $marketName
     * @return EurHistory[]
     */
    public function findTwoLastData($marketName): array
    {
        // automatically knows to select Eurhistories
        // the "me" is an alias you'll use in the rest of the query
        $qb = $this->createQueryBuilder('me')
            ->select('me.spot as spot')
            ->andWhere('me.market = :market')
            ->setParameter('market', $marketName)
            ->orderBy('me.insert_time', 'DESC')
            ->setMaxResults(2)
            ->getQuery();

        return $qb->execute();
    }

    /**
     * @param $time_value
     * @param $market_name
     * @return EurHistory
     */
    public function findPeriodFirstSpot($time_value, $market_name): array
    {
        // automatically knows to select Eurhistories
        // the "me" is an alias you'll use in the rest of the query
        $qb = $this->createQueryBuilder('me')
            ->select('me.spot as spot')
            ->andWhere('me.insert_time >= :time_value')
            ->andWhere('me.market = :market_name')
            ->setParameter('time_value', $time_value)
            ->setParameter('market_name', $market_name)
            ->orderBy('me.insert_time', 'ASC')
            ->setMaxResults(1)
            ->getQuery();

        return $qb->execute();
    }


    /**
     * @param $start_period
     * @param $end_period
     * @param $market_name
     * @return EurHistory
     */
    public function findPeriodHighLowSpot($start_period, $end_period, $market_name): array
    {
        $qb = $this->createQueryBuilder('me')
            ->select('MAX(me.spot) AS high_value, MIN(me.spot) as low_value')
            ->andWhere('me.insert_time >= :start_time')
            ->andWhere('me.insert_time <= :end_time')
            ->andWhere('me.market = :market_name')
            ->setParameter('start_time', $start_period)
            ->setParameter('end_time', $end_period)
            ->setParameter('market_name', $market_name)
            ->groupBy('me.market')
            ->getQuery();

        return $qb->execute();
    }

    /**
     * @param $end_period
     * @param $market_name
     * @return EurHistory
     */
    public function findPeriodLastInsertTime($end_period, $market_name): array
    {
        $qb = $this->createQueryBuilder('me')
            ->select('MAX(me.insert_time) AS last_time')
            ->andWhere('me.insert_time <= :end_time')
            ->andWhere('me.market = :market_name')
            ->setParameter('end_time', $end_period)
            ->setParameter('market_name', $market_name)
            ->groupBy('me.market')
            ->getQuery();

        return $qb->execute();
    }

}
