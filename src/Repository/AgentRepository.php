<?php

namespace App\Repository;

use App\Entity\Agent;
use App\Entity\Waypoint;
use App\Helper\Paginator\PaginatorOptions;
use App\Helper\Paginator\PaginatorRepoTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Waypoint|null find($id, $lockMode = null, $lockVersion = null)
 * @method Waypoint|null findOneBy(array $criteria, array $orderBy = null)
 * @method Waypoint[]    findAll()
 * @method Waypoint[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AgentRepository extends ServiceEntityRepository
{
    use PaginatorRepoTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Agent::class);
    }

    /**
     * @return Agent[] Returns an array of Agent objects
     */
    public function findLatLon()
    {
        $result = $this->createQueryBuilder('w')
            ->select("CONCAT(w.lat, ',', w.lon) AS lat_lon")
            ->getQuery()
            ->getResult();

        return array_column($result, 'lat_lon');
    }

    /**
     * @param PaginatorOptions $options
     *
     * @return Paginator
     */
    public function getRawList(PaginatorOptions $options): Paginator
    {
        $criteria = $options->getCriteria();

        $query = $this->createQueryBuilder('w')
            ->orderBy('w.'.$options->getOrder(), $options->getOrderDir());

        if (isset($criteria['province']) && $criteria['province']) {
            $query->where('w.province = :province')
                ->setParameter('province', $criteria['province']);
        }

        if ($options->searchCriteria('city')) {
            $query->andWhere('w.city LIKE :city')
                ->setParameter('city', '%'.$options->searchCriteria('city').'%');
        }

        if ($options->searchCriteria('store')) {
            $query->andWhere('t.store = :store')
                ->setParameter('store', (int)$options->searchCriteria('store'));
        }

        if ($options->searchCriteria('date_from')) {
            $query->andWhere('t.date >= :date_from')
                ->setParameter('date_from', $options->searchCriteria('date_from'));
        }

        if ($options->searchCriteria('date_to')) {
            $query->andWhere('t.date <= :date_to')
                ->setParameter('date_to', $options->searchCriteria('date_to'));
        }

        $query = $query->getQuery();

        return $this->paginate($query, $options->getPage(), $options->getLimit());
    }

    public function getAgentData()
    {
        return $this->createQueryBuilder('a')
            ->select('a.name, a.lat, a.lon as lng')
            ->orderBy('a.name')
            ->getQuery()
            ->execute();
    }
}
