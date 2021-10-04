<?php

namespace App\Repository;

use App\Entity\BitfinexEurHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method BitfinexEurHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method BitfinexEurHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method BitfinexEurHistory[]    findAll()
 * @method BitfinexEurHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BitfinexEurHistoryRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, BitfinexEurHistory::class);
    }

    /**
     * @return BitfinexEurHistory
     */
    public function findLastData(): array
    {
        // automatically knows to select BitfinexEurHistories
        // the "me" is an alias you'll use in the rest of the query
        $qb = $this->createQueryBuilder('me')
            ->select('me.spot as spot')
            ->orderBy('me.insert_time', 'DESC')
            ->setMaxResults(1)
            ->getQuery();

        return $qb->execute();
    }

    /**
     * @return BitfinexEurHistory[]
     */
    public function findTwoLastData(): array
    {
        // automatically knows to select BitfinexEurHistories
        // the "me" is an alias you'll use in the rest of the query
        $qb = $this->createQueryBuilder('me')
            ->select('me.spot as spot')
            ->orderBy('me.insert_time', 'DESC')
            ->setMaxResults(2)
            ->getQuery();

        return $qb->execute();
    }

    /**
     * @param $time_value
     * @return BitfinexEurHistory
     */
    public function findPeriodFirstSpot($time_value): array
    {
        $qb = $this->createQueryBuilder('me')
            ->select('me.spot as spot')
            ->andWhere('me.insert_time >= :time_value')
            ->setParameter('time_value', $time_value)
            ->orderBy('me.insert_time', 'ASC')
            ->setMaxResults(1)
            ->getQuery();

        return $qb->execute();
    }

    /**
     * @param $start_period
     * @param $end_period
     * @return BitfinexEurHistory
     */
    public function findPeriodHighLowSpot($start_period, $end_period): array
    {
        $qb = $this->createQueryBuilder('me')
            ->select('MAX(me.spot) AS high_value, MIN(me.spot) as low_value')
            ->andWhere('me.insert_time >= :start_time')
            ->andWhere('me.insert_time <= :end_time')
            ->setParameter('start_time', $start_period)
            ->setParameter('end_time', $end_period)
            ->getQuery();

        return $qb->execute();
    }

    /**
     * @param $end_period
     * @return BitfinexEurHistory
     */
    public function findPeriodLastInsertTime($end_period): array
    {
        $qb = $this->createQueryBuilder('me')
            ->select('MAX(me.insert_time) AS last_time')
            ->andWhere('me.insert_time <= :end_time')
            ->setParameter('end_time', $end_period)
            ->getQuery();

        return $qb->execute();
    }

}
