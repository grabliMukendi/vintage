<?php

namespace App\Controller;

use App\Entity\Produits;
use App\Services\PaginatorByFilter;
use App\Repository\CouleurRepository;
use App\Repository\ProduitsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CouleurController extends AbstractController
{
    /**
     * @Route("/couleur", name="couleur")
     */
    public function allCouleurs(CouleurRepository $repo)
    {
        $couleurs = $repo->findDistinctColors();
        return $this->render('couleur/couleur_list.html.twig', [
            'couleurs' => $couleurs,
        ]);
    }

    /**
     * @Route("/couleur/tendance", name="couleur_by_tendance")
     */
    public function couleursByTendance(ProduitsRepository $repo)
    {
        return $this->render('couleur/couleur_by_tendance.html.twig', 
        [
            'produits' => $repo->findCouleurByRubrique('tendance'),
        ]);
    }
    
    /**
     * @Route("/couleur/art/deco", name="couleur_by_art_deco")
     */
    public function couleursByArtDeco(ProduitsRepository $repo)
    {
        return $this->render('couleur/couleur_by_art_deco.html.twig', 
        [
            'produits' => $repo->findCouleurByRubrique('art déco'),
        ]);
    }

    /**
     * @Route("/couleur/automobile", name="couleur_by_automobile")
     */
    public function couleursByAutomobile(ProduitsRepository $repo)
    {
        return $this->render('couleur/couleur_by_automobile.html.twig', 
        [
            'produits' => $repo->findCouleurByRubrique('automobile'),
        ]);
    }

    /**
     * @Route("/couleur/sport/loisir", name="couleur_by_sport_loisir")
     */
    public function couleursBySportLoisir(ProduitsRepository $repo)
    {
        return $this->render('couleur/couleur_by_sport_loisir.html.twig', 
        [
            'produits' => $repo->findCouleurByRubrique('sport et loisir'),
        ]);
    }

    /**
     * @Route("/couleur/{color}/{rubrique}/{page<\d+>?1}", name="couleur_tendance_products")
     */
    public function tendanceByColorAction($color, $rubrique, SessionInterface $session, $page, PaginatorByFilter $pagination, Request $request) 
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

        $couleurs = $pagination->findByCouleur($color, $rubrique);
        
        if(!$couleurs) {
            throw $this->createNotFoundException('Aïe! La Page n\'existe pas !');
        }

        if($request->isXmlHttpRequest()) 
        {
            //dd($color);
            $content = $this->renderView('couleur/couleurs_js_filters.html.twig', 
            [
                'produits' => $couleurs, // Affichage des produits par catégorie
                'stringSaisie' => $stringSaisie,
                'nbSearch' => $nbSearch,
                'panier' => $panier,
            ]);

            if(count($couleurs) != 0) 
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
            $total = count($couleurs);
            //dd($couleurs);
            return $this->render('couleur/products_by_color.html.twig', 
            [
                'produits' => $couleurs, // Affichage de tous les produits
                'page' => $page,
                'pages' => ceil($total / $pagination->getLimit()),
                'colorName' => $color,
                'rubrique' => $rubrique,
                'stringSaisie' => $stringSaisie,
                'nbSearch' => $nbSearch,
                'panier' => $panier,
            ]);
        }

    }

}
