<?php

namespace App\Controller;

use App\Entity\Produits;
use App\Services\PaginatorByFilter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SportLoisirController extends AbstractController
{
    /**
     * @Route("/rubriques/sport/loisir/{page<\d+>?1}", name="rubrique_sport_loisir")
     * Route qui mène vers la page de la rubrique
     */
    public function sportLoisirAction(SessionInterface $session, $page, PaginatorByFilter $pagination) 
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
        
        return $this->render('rubriques/sport_loisir.html.twig', 
        [
            'produits' => $pagination->data('rubrique', 'sport et loisir', 'id', 'DESC'), // Affichage de tous les produits
            'page' => $page,
            'pages' => $pagination->pages('rubrique', 'sport et loisir'),
            'stringSaisie' => $stringSaisie,
            'nbSearch' => $nbSearch,
            'panier' => $panier,
        ]);

    }

    /**
     * @Route("/rubrique/sport/loisir/fourchette-prix/{page<\d+>?1}", name="sport_loisir_show_price")
     */
    public function showSportLoisirPriceAction(Request $request, SessionInterface $session, $page, PaginatorByFilter $pagination) 
    {
        $pagination ->setEntity(Produits::class)
                    ->setPage($page);

        $price = $request->get('min_price');
        $produits = $pagination->getByPrice($price, 'sport et loisir');

        if($request->isXmlHttpRequest()) 
        {
            if($session->has('panier')) 
            {
                $panier = $session->get('panier');
            } else {
                $panier = false;
            }

            $total = count($produits);
            $content = $this->renderView('produits/show_price.html.twig', 
            [
                'produits' => $produits,
                'panier' => $panier,
                'page' => $page,
                'pages' => ceil($total / $pagination->getLimit()),
            ]);

            if(count($produits) != 0) 
            {
                return new JsonResponse([
                    'success' => 'ok',
                    'results' => $content
                ], 200);
            } else 
            {
                return new JsonResponse([
                    'errors' => '<strong>Désolé !</strong> Aucun produit ne correspond à cette fourchette de prix.'
                ]);
            }
        }   
    }

    /**
     * @Route("/rubrique/sport/loisir/prix-croissant/{page<\d+>?1}", name="sport_loisir_prix_croissant")
     */
    public function artDecoPrixCroissantAction(SessionInterface $session, Request $request, $page, PaginatorByFilter $pagination) 
    {
        $pagination ->setEntity(Produits::class)
                    ->setPage($page);
                    
        $stringSaisie = null;
        $nbSearch = null;
        $minPrice = $pagination->getPrixCroissant('rubrique', 'sport et loisir');

        if($session->has('panier')) 
        {
            $panier = $session->get('panier');
        } else {
            $panier = false;
        }

        if($request->isXmlHttpRequest()) 
        {
            $content = $this->renderView('rubriques/min_price_js_rubriques.html.twig', 
            [
                'produits' => $minPrice,
                'stringSaisie' => $stringSaisie,
                'nbSearch' => $nbSearch,
                'panier' => $panier,
                'page' => $page,
            ]);

            if(count($minPrice) != 0) 
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
            return $this->render('rubriques/sport_loisir.html.twig', 
            [
                'produits' => $minPrice,
                'stringSaisie' => $stringSaisie,
                'nbSearch' => $nbSearch,
                'panier' => $panier,
                'page' => $page,
                'pages' => $pagination->pages('rubrique', 'sport et loisir'),
            ]);
        }
    }

    /**
     * @Route("/rubrique/art/deco/prix-decroissant/{page<\d+>?1}", name="sport_loisir_prix_decroissant")
     */
    public function artDecoPrixDecroissantAction(SessionInterface $session, Request $request, $page, PaginatorByFilter $pagination) 
    {
        $stringSaisie = null;
        $nbSearch = null;
        $pagination ->setEntity(Produits::class)
                    ->setPage($page);

        $stringSaisie = null;
        $nbSearch = null;
        $maxPrice = $pagination->getPrixDecroissant('rubrique', 'sport et loisir');

        if($session->has('panier')) 
        {
            $panier = $session->get('panier');
        } else {
            $panier = false;
        }

        if($request->isXmlHttpRequest()) 
        {
            $content = $this->renderView('rubriques/max_price_js_rubriques.html.twig', 
            [
                'produits' => $maxPrice,
                'stringSaisie' => $stringSaisie,
                'nbSearch' => $nbSearch,
                'panier' => $panier,
                'page' => $page,
            ]);

            if(count($maxPrice) != 0) 
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
            return $this->render('rubriques/sport_loisir.html.twig', 
            [
                'produits' => $maxPrice, // Affichage des produits par catégorie
                'stringSaisie' => $stringSaisie,
                'nbSearch' => $nbSearch,
                'panier' => $panier,
                'page' => $page,
                'pages' => $pagination->pages('rubrique', 'sport et loisir'),
            ]);
        }
    }

}
