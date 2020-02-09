<?php

namespace App\Controller;

use App\Entity\Produits;
use App\Services\PaginatorByFilter;
use App\Repository\ProduitsRepository;
use App\Repository\CategoriesRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategorieController extends AbstractController
{
    /**
     * @Route("/categorie", name="categorie")
     */
    public function allCategories(CategoriesRepository $repo)
    {
        return $this->render('categorie/categorie_list.html.twig', 
        [
            'categories' => $repo->findDistinctCategorie(),
        ]);
    }

    /**
     * @Route("/categorie/tendance", name="categorie_by_tendance")
     */
    public function categoriesByTendance(ProduitsRepository $repo)
    {
        return $this->render('categorie/categorie_by_tendance.html.twig', 
        [
            'produits' => $repo->findCategorieByRubrique('tendance'),
        ]);
    }

    /**
     * @Route("/categorie/art/deco", name="categorie_by_art_deco")
     */
    public function categoriesByArtDeco(ProduitsRepository $repo)
    {
        return $this->render('categorie/categorie_by_art_deco.html.twig', 
        [
            'produits' => $repo->findCategorieByRubrique('art déco'),
        ]);
    }

    /**
     * @Route("/categorie/sport/loisir", name="categorie_by_sport_loisir")
     */
    public function categoriesBySportLoisir(ProduitsRepository $repo)
    {
        return $this->render('categorie/categorie_by_sport_losir.html.twig', 
        [
            'produits' => $repo->findCategorieByRubrique('sport et loisir'),
        ]);
    }

    /**
     * @Route("/categorie/automobile", name="categorie_by_automobile")
     */
    public function categoriesByAautomobile(ProduitsRepository $repo)
    {
        return $this->render('categorie/categorie_by_automobile.html.twig', 
        [
            'produits' => $repo->findCategorieByRubrique('automobile'),
        ]);
    }

    /**
     * @Route("/categorie/{cat}/{rubrique}/{page<\d+>?1}", name="categorie_tendance_products")
     */
    public function tendanceByCategorieAction($cat, $rubrique, SessionInterface $session, $page, PaginatorByFilter $pagination, Request $request) 
    {
        $pagination ->setEntity(Produits::class)
                    ->setPage($page);

        $stringSaisie = null;
        $nbSearch = null;

        if($session->has('panier')) 
        {
            $panier = $session->get('panier');
        } else {
            $panier = false;
        }

        $categorie = $pagination->findByCategorie($cat, $rubrique);
        if(!$categorie) {
            throw $this->createNotFoundException('Aïe! La Page n\'existe pas !');
        }

        if($request->isXmlHttpRequest()) 
        {
            //dd($cat);
            $content = $this->renderView('categorie/categories_js_filters.html.twig', 
            [
                'produits' => $categorie, // Affichage des produits par catégorie
                'stringSaisie' => $stringSaisie,
                'nbSearch' => $nbSearch,
                'panier' => $panier,
            ]);

            if(count($categorie) != 0) 
            {
                sleep(1);
                return new JsonResponse(
                [
                    'success' => 'OK',
                    'results' => $content
                ], 200);
            } else 
            {
                return new JsonResponse(
                [
                    'errors' => '<strong>Désolé !</strong> Aucun produit ne correspond à votre recherche.'
                ]);
            }
        } else 
        {
            $total = count($categorie);
            //dd($categorie);
            return $this->render('categorie/products_by_category.html.twig', 
            [
                'produits' => $categorie, // Affichage de tous les produits
                'page' => $page,
                'pages' => ceil($total / $pagination->getLimit()),
                'catName' => $cat,
                'rubrique' => $rubrique,
                'stringSaisie' => $stringSaisie,
                'nbSearch' => $nbSearch,
                'panier' => $panier,
            ]);
        }

    }

}
