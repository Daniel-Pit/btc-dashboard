<?php

namespace App\Repository;

use App\Entity\BtcMarketData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method BtcMarketData|null find($id, $lockMode = null, $lockVersion = null)
 * @method BtcMarketData|null findOneBy(array $criteria, array $orderBy = null)
 * @method BtcMarketData[]    findAll()
 * @method BtcMarketData[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BtcMarketDataRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, BtcMarketData::class);
    }


    /**
     * @param $marketName
     * @param $currency
     * @return BtcMarketData
     */
    public function findMarketCurrencyData($marketName, $currency): array
    {
        $qb = $this->createQueryBuilder('bmd')
            ->select('bmd.ago_1w_change as ago_1w_change, bmd.ago_1w_high as ago_1w_high, bmd.ago_1w_low as ago_1w_low, bmd.ago_1m_change as ago_1m_change, bmd.ago_1m_high as ago_1m_high, bmd.ago_1m_low as ago_1m_low, bmd.ago_3m_change as ago_3m_change, bmd.ago_3m_high as ago_3m_high, bmd.ago_3m_low as ago_3m_low, bmd.ago_6m_change as ago_6m_change, bmd.ago_6m_high as ago_6m_high, bmd.ago_6m_low as ago_6m_low, bmd.ago_1y_change as ago_1y_change, bmd.ago_1y_high as ago_1y_high, bmd.ago_1y_low as ago_1y_low')
            ->andWhere('bmd.currency = :currency_pair')
            ->andWhere('bmd.market = :market')
            ->setParameter('currency_pair', $currency)
            ->setParameter('market', $marketName)
            ->setMaxResults(1)
            ->getQuery();

        return $qb->execute();
    }

}
