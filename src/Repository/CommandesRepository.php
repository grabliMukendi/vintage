<?php

namespace App\Repository;

use App\Entity\Commandes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Commandes|null find($id, $lockMode = null, $lockVersion = null)
 * @method Commandes|null findOneBy(array $criteria, array $orderBy = null)
 * @method Commandes[]    findAll()
 * @method Commandes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommandesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commandes::class);
    }

    public function getCommandesCount() 
    {
        return $this->createQueryBuilder('c')
            ->select('COUNT(c)')
            ->where('c.valider = 1')
            ->andWhere('c.reference != 0')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function getByCommande() 
    {
        return $this->createQueryBuilder('c')
            ->select('c.commande')
            ->where('c.valider = 1')
            ->andWhere('c.reference != 0')
            ->getQuery()
            ->getResult()
        ;
    }

    public function byFacture($user) 
    {
        return $this->createQueryBuilder('c')
            ->select('c')
            ->where('c.user = :user')
            ->andWhere('c.valider = 1')
            ->andWhere('c.reference != 0')
            ->setParameter('user', $user)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

    // /**
    //  * @return Commandes[] Returns an array of Commandes objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Commandes
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
