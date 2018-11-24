<?php

namespace App\Repository;

use App\Entity\Waypoint;
use App\Helper\Paginator\PaginatorOptions;
use App\Helper\Paginator\PaginatorRepoTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Waypoint|null find($id, $lockMode = null, $lockVersion = null)
 * @method Waypoint|null findOneBy(array $criteria, array $orderBy = null)
 * @method Waypoint[]    findAll()
 * @method Waypoint[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WaypointRepository extends ServiceEntityRepository
{
    use PaginatorRepoTrait;

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Waypoint::class);
    }

    /**
     * @return Waypoint[] Returns an array of Waypoint objects
     */
    public function findById($id)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('w.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Waypoint[] Returns an array of Waypoint objects
     */
    public function findLatLon()
    {
        $result = $this->createQueryBuilder('w')
            ->select("CONCAT(w.lat, ',', w.lon) AS lat_lon")
            ->getQuery()
            ->getResult();

        return array_column($result, 'lat_lon');
    }

//    /**
//     * @return Waypoint[] Returns an array of Waypoint objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('w.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Waypoint
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

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

    public function findCities()
    {
        $result = $this->createQueryBuilder('w')
            ->select('w.city')
            ->distinct()
            ->getQuery()
            ->getResult();

        $cities = array_column($result, 'city');

        $cities = array_filter($cities);

        sort($cities);

        return $cities;
    }
}
