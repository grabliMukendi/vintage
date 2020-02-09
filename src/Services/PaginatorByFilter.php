<?php

namespace App\Services;

use Twig\Environment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class PaginatorByFilter 
{
    private $entity;
    private $limit = 10;
    private $currentPage = 1;
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }
    
    public function getEntity() 
    {
        return $this->entity;
    }

    public function setEntity($entity) 
    {
        $this->entity = $entity;
        return $this;
    }

    public function getLimit() 
    {
        return $this->limit;
    }

    public function setLimit($limit) 
    {
        $this->limit = $limit;
        return $this;
    }

    public function getPage() 
    {
        return $this->currentPage;
    }

    public function setPage($currentPage) 
    {
        $this->currentPage = $currentPage;
        return $this;
    }

    public function pages($field, $criteria) 
    {
        // Connaître le total des enregistrements
        $repo = $this->manager->getRepository($this->entity);
        $total = count($repo->findBy([ $field => $criteria ]));

        // faire la division, l'arrondi et le renvoyer
        $pages = ceil($total / $this->limit);

        return $pages;
    }

    public function data($field, $criteria, $id, $desc) 
    {
        // Calculer l'offset
        $offset = $this->currentPage * $this->limit - $this->limit;

        // Demander au Repository de trouver les éléments
        $repo = $this->manager->getRepository($this->entity);
        $data = $repo->findBy([ $field => $criteria ], [$id => $desc], $this->limit, $offset);

        // Renvoyer les éléments en question
        return $data;

    }

    public function findByCategorie($categorie, $rubrique) 
    {
        // Calculer l'offset
        $offset = $this->currentPage * $this->limit - $this->limit;

        // Demander au Repository de trouver les éléments
        $repo = $this->manager->getRepository($this->entity);
        $data = $repo   ->createQueryBuilder('p')
                        ->select('p', 'c')
                        ->join('p.categorie', 'c')
                        ->andWhere('c.nom IN (:categorie)')
                        ->setParameter('categorie', $categorie)
                        ->andWhere('p.rubrique = :rubrique')
                        ->setParameter('rubrique', $rubrique)
                        ->orderBy('p.id', 'DESC')
                        ->setFirstResult($offset)
                        ->setMaxResults($this->limit)
                        ->getQuery()
                        ->getResult()
                    ;

        // Renvoyer les éléments en question
        return $data;
    }

    public function findByCouleur($color, $rubrique) 
    {
        // Calculer l'offset
        $offset = $this->currentPage * $this->limit - $this->limit;

        // Demander au Repository de trouver les éléments
        $repo = $this->manager->getRepository($this->entity);
        $data = $repo   ->createQueryBuilder('p')
                        ->leftJoin('p.couleur', 'couleur')
                        ->andWhere('couleur.nom = :nom')
                        ->setParameter('nom', $color)
                        ->andWhere('p.rubrique = :rubrique')
                        ->setParameter('rubrique', $rubrique)
                        ->orderBy('p.id', 'DESC')
                        ->setFirstResult($offset)
                        ->setMaxResults($this->limit)
                        ->getQuery()
                        ->getResult()
                    ;

        // Renvoyer les éléments en question
        return $data;
    }

    public function findByTaille($size, $rubrique) 
    {
        // Calculer l'offset
        $offset = $this->currentPage * $this->limit - $this->limit;

        // Demander au Repository de trouver les éléments
        $repo = $this->manager->getRepository($this->entity);
        $data = $repo   ->createQueryBuilder('p')
                        ->leftJoin('p.taille', 'taille')
                        ->andWhere('taille.nom = :nom')
                        ->setParameter('nom', $size)
                        ->andWhere('p.rubrique = :rubrique')
                        ->setParameter('rubrique', $rubrique)
                        ->orderBy('p.id', 'DESC')
                        ->setFirstResult($offset)
                        ->setMaxResults($this->limit)
                        ->getQuery()
                        ->getResult()
                    ;

        // Renvoyer les éléments en question
        return $data;
    }

    public function getByPrice($price, $rubrique) 
    {
        // Calculer l'offset
        $offset = $this->currentPage * $this->limit - $this->limit;

        // Demander au Repository de trouver les éléments
        $repo = $this->manager->getRepository($this->entity);
        $data = $repo->createQueryBuilder('p')
            ->where('p.prix >= :prix')
            ->setParameter('prix', $price)
            ->andWhere('p.rubrique = :rubrique')
            ->setParameter('rubrique', $rubrique)
            ->orderBy('p.prix', 'ASC')
            ->setFirstResult($offset)
            ->setMaxResults($this->limit)
            ->getQuery()
            ->getResult();

        // Renvoyer les éléments en question
        return $data;
    }

    public function getPrixCroissant($field, $criteria) 
    {
        // Calculer l'offset
        $offset = $this->currentPage * $this->limit - $this->limit;

        // Demander au Repository de trouver les éléments
        $repo = $this->manager->getRepository($this->entity);
        $data = $repo->findBy([ $field => $criteria ], ['prix' => 'ASC'], $this->limit, $offset);

        // Renvoyer les éléments en question
        return $data;

    }

    public function getPrixDecroissant($field, $criteria) 
    {
        // Calculer l'offset
        $offset = $this->currentPage * $this->limit - $this->limit;

        // Demander au Repository de trouver les éléments
        $repo = $this->manager->getRepository($this->entity);
        $data = $repo->findBy([ $field => $criteria ], ['prix' => 'DESC'], $this->limit, $offset);

        // Renvoyer les éléments en question
        return $data;

    }

}