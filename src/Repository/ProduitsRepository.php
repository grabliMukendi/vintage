<?php

namespace App\Repository;

use App\Entity\Produits;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Produits|null find($id, $lockMode = null, $lockVersion = null)
 * @method Produits|null findOneBy(array $criteria, array $orderBy = null)
 * @method Produits[]    findAll()
 * @method Produits[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produits::class);
    }

    public function getProductsCount() 
    {
        return $this->createQueryBuilder('p')
            ->select('sum(p.disponible)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function findCategorieByItem($item) 
    {
        return $this->createQueryBuilder('p')
            ->select('p', 'c')
            ->join('p.categorie', 'c')
            ->groupBy('c.nom')
            ->andWhere('p.rubrique = :rubrique')
            ->setParameter('rubrique', $item)
            ->orderBy('p.id', 'DESC')
            ->setMaxResults(4)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findOtherCategorie($categorie, $id) 
    {
        return $this->createQueryBuilder('p')
            ->select('p', 'c')
            ->join('p.categorie', 'c')
            ->where('c.nom IN (:categorie)')
            ->setParameter('categorie', $categorie)
            ->andWhere('p.id != :id')
            ->setParameter('id', $id)
            ->orderBy('p.id', 'DESC')
            ->setMaxResults(3)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByCategorie($categorie) 
    {
        return $this->createQueryBuilder('p')
            ->select('p', 'c')
            ->join('p.categorie', 'c')
            ->andWhere('c.nom IN (:categorie)')
            ->setParameter('categorie', $categorie)
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findCategorieByRubrique($rubrique) 
    {
        return $this->createQueryBuilder('p')
            ->select('p', 'c')
            ->join('p.categorie', 'c')
            ->groupBy('c.nom')
            ->andWhere('p.rubrique = :rubrique')
            ->setParameter('rubrique', $rubrique)
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findCouleurByRubrique($rubrique) 
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.couleur', 'couleur')
            ->groupBy('couleur.nom')
            ->andWhere('p.rubrique = :rubrique')
            ->setParameter('rubrique', $rubrique)
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findTailleByRubrique($rubrique) 
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.taille', 'taille')
            ->groupBy('taille.nom')
            ->andWhere('p.rubrique = :rubrique')
            ->setParameter('rubrique', $rubrique)
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByCouleur($color) 
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.couleur', 'couleur')
            ->andWhere('couleur.nom = :nom')
            ->setParameter('nom', $color)
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByTaille($size) 
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.taille', 'taille')
            ->andWhere('taille.nom = :nom')
            ->setParameter('nom', $size)
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getByPrice($price) 
    {
        return $this->createQueryBuilder('p')
            ->where('p.prix >= :prix')
            ->setParameter('prix', $price)
            ->orderBy('p.prix', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function searchProducts($str)
    {
        return $this->createQueryBuilder('p')
            ->where('p.titre LIKE :titre')
            ->setParameter( 'titre', "%$str%")
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function prixCroissant()
    {
        return $this->createQueryBuilder('p')
            ->select('p')
            ->orderBy('p.prix', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function prixDecroissant()
    {
        return $this->createQueryBuilder('p')
            ->select('p')
            ->orderBy('p.prix', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /*public function findAllCouleurs() 
    {
        return $this->createQueryBuilder('p')
        ->select('p.couleur')
        ->distinct(true)
        ->orderBy('p.couleur')
        ->getQuery()
        ->getResult();
    }*/

    // /**
    //  * @return Produits[] Returns an array of Produits objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Produits
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
