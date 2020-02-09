<?php

namespace App\Services;

use Twig\Environment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class Paginator 
{
    private $entity;
    private $limit = 10;
    private $currentPage = 1;
    private $manager;
    private $twig;
    private $route;

    public function __construct(EntityManagerInterface $manager, Environment $twig, RequestStack $request)
    {
        $this->manager = $manager;
        $this->twig = $twig;
        $this->route = $request->getCurrentRequest()->attributes->get('_route');
    }

    public function getData() 
    {
        // Calculer l'offset
        $offset = $this->currentPage * $this->limit - $this->limit;

        // Demander au Repository de trouver les éléments
        $repo = $this->manager->getRepository($this->entity);
        $data = $repo->findBy([], [], $this->limit, $offset);

        // Renvoyer les éléments en question
        return $data;

    }

    public function getRoute() 
    {
        return $this->route;
    }

    public function setRoute($route) 
    {
        $this->route = $route;
        return $this;
    }

    public function display() 
    {
       return $this->twig->display('partials/pagination.html.twig', 
       [
            'page' => $this->currentPage,
            'pages' => $this->getPages(),
            'route' => $this->route,
       ]);
    }

    public function getPages() 
    {
        // Connaître le total des enregistrements
        $repo = $this->manager->getRepository($this->entity);
        $total = count($repo->findAll());

        // faire la division, l'arrondi et le renvoyer
        $pages = ceil($total / $this->limit);

        return $pages;
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

    public function getPrixCroissant() 
    {
        // Calculer l'offset
        $offset = $this->currentPage * $this->limit - $this->limit;

        // Demander au Repository de trouver les éléments
        $repo = $this->manager->getRepository($this->entity);
        $data = $repo->findBy([], ['prix' => 'ASC'], $this->limit, $offset);

        // Renvoyer les éléments en question
        return $data;

    }

    public function getPrixDecroissant() 
    {
        // Calculer l'offset
        $offset = $this->currentPage * $this->limit - $this->limit;

        // Demander au Repository de trouver les éléments
        $repo = $this->manager->getRepository($this->entity);
        $data = $repo->findBy([], ['prix' => 'DESC'], $this->limit, $offset);

        // Renvoyer les éléments en question
        return $data;

    }

    public function searchProducts($str)
    {
        $repo = $this->manager->getRepository($this->entity);

        $offset = $this->currentPage * $this->limit - $this->limit;

        $data = $repo->createQueryBuilder('p')
            ->where('p.titre LIKE :titre')
            ->setParameter( 'titre', "%$str%")
            ->orderBy('p.id', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($this->limit)
            ->getQuery()
            ->getResult()
        ;
        //dd($data);
        return $data;
    }

    public function findByCategorie($categorie) 
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
                        ->orderBy('p.id', 'DESC')
                        ->setFirstResult($offset)
                        ->setMaxResults($this->limit)
                        ->getQuery()
                        ->getResult()
                    ;

        // Renvoyer les éléments en question
        return $data;
    }

    public function findByCouleur($color) 
    {
        // Calculer l'offset
        $offset = $this->currentPage * $this->limit - $this->limit;

        // Demander au Repository de trouver les éléments
        $repo = $this->manager->getRepository($this->entity);
        $data = $repo   ->createQueryBuilder('p')
                        ->leftJoin('p.couleur', 'couleur')
                        ->andWhere('couleur.nom = :nom')
                        ->setParameter('nom', $color)
                        ->orderBy('p.id', 'DESC')
                        ->setFirstResult($offset)
                        ->setMaxResults($this->limit)
                        ->getQuery()
                        ->getResult()
                    ;

        // Renvoyer les éléments en question
        return $data;
    }

    public function findByTaille($size) 
    {
        // Calculer l'offset
        $offset = $this->currentPage * $this->limit - $this->limit;

        // Demander au Repository de trouver les éléments
        $repo = $this->manager->getRepository($this->entity);
        $data = $repo   ->createQueryBuilder('p')
                        ->leftJoin('p.taille', 'taille')
                        ->andWhere('taille.nom = :nom')
                        ->setParameter('nom', $size)
                        ->orderBy('p.id', 'DESC')
                        ->setFirstResult($offset)
                        ->setMaxResults($this->limit)
                        ->getQuery()
                        ->getResult()
                    ;

        // Renvoyer les éléments en question
        return $data;
    }

}