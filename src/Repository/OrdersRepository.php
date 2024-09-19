<?php

namespace App\Repository;

use DateTime;
use App\Entity\Orders;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Orders>
 */
class OrdersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Orders::class);
    }

    public function save(Orders $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Orders $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findOrdersByUser($userId): array
    {
        return $this->createQueryBuilder('o')
            ->where('o.userId = :val')
            ->setParameter('val', $userId)
            ->getQuery()
            ->getResult();
    }

    public function countByDate(DateTime $dateTime)
    {
        return $this->createQueryBuilder('o')
            ->select('count(o.id)')
            ->where('o.date = :date')
            ->setParameter('date', $dateTime)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findOrdersByStatusAndClientName(?string $status, ?string $searchTerm): array
    {
        $queryBuilder = $this->createQueryBuilder('o')
            ->leftJoin('o.userId', 'u')
            ->where('1=1'); 

        if ($status) {
            $queryBuilder
                ->andWhere('o.status LIKE :status')
                ->setParameter('status', '%' . $status . '%');
        }

        if ($searchTerm) {
            $queryBuilder
                ->andWhere('u.name LIKE :searchTerm OR u.firstName LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }

        return $queryBuilder
            ->getQuery()
            ->getResult();
    }
}
