<?php

namespace App\Repository;

use App\Entity\Langages;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Langages|null find($id, $lockMode = null, $lockVersion = null)
 * @method Langages|null findOneBy(array $criteria, array $orderBy = null)
 * @method Langages[]    findAll()
 * @method Langages[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LangagesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Langages::class);
    }

    // /**
    //  * @return Langages[] Returns an array of Langages objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Langages
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
