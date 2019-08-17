<?php

namespace App\Repository;

use App\Entity\Emeteur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Emeteur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Emeteur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Emeteur[]    findAll()
 * @method Emeteur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmeteurRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Emeteur::class);
    }

    // /**
    //  * @return Emeteur[] Returns an array of Emeteur objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Emeteur
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
