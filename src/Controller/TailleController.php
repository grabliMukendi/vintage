<?php

namespace App\Controller;

use App\Entity\Produits;
use App\Services\PaginatorByFilter;
use App\Repository\TailleRepository;
use App\Repository\ProduitsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TailleController extends AbstractController
{
    /**
     * @Route("/taille", name="taille")
     */
    public function allTailles(TailleRepository $repo)
    {
        $tailles = $repo->findDistinctTaille();
        return $this->render('taille/taille_list.html.twig', [
            'tailles' => $tailles,
        ]);
    }

    /**
     * @Route("/taille/tendance", name="taille_tendance")
     */
    public function tailleByTendance(ProduitsRepository $repo)
    {
        return $this->render('taille/taille_by_tendance.html.twig', 
        [
            'produits' => $repo->findTailleByRubrique('tendance'),
        ]);
    }

    /**
     * @Route("/taille/sport/loisir", name="taille_sport_loisir")
     */
    public function tailleBySportLoisir(ProduitsRepository $repo)
    {
        return $this->render('taille/taille_by_sport_loisir.html.twig', 
        [
            'produits' => $repo->findTailleByRubrique('sport et loisir'),
        ]);
    }

    /**
     * @Route("/taille/{size}/{rubrique}/{page<\d+>?1}", name="taille_tendance_products")
     */
    public function tendanceBySizeAction($size, $rubrique, SessionInterface $session, $page, PaginatorByFilter $pagination, Request $request) 
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

        $tailles = $pagination->findByTaille($size, $rubrique);
        
        if(!$tailles) {
            throw $this->createNotFoundException('Aïe! La Page n\'existe pas !');
        }

        if($request->isXmlHttpRequest()) 
        {
            //dd($color);
            $content = $this->renderView('taille/tailles_js_filters.html.twig', 
            [
                'produits' => $tailles, // Affichage des produits par catégorie
                'stringSaisie' => $stringSaisie,
                'nbSearch' => $nbSearch,
                'panier' => $panier,
            ]);

            if(count($tailles) != 0) 
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
            $total = count($tailles);
            //dd($tailles);
            return $this->render('taille/products_by_size.html.twig', 
            [
                'produits' => $tailles, // Affichage de tous les produits
                'page' => $page,
                'pages' => ceil($total / $pagination->getLimit()),
                'sizeName' => $size,
                'rubrique' => $rubrique,
                'stringSaisie' => $stringSaisie,
                'nbSearch' => $nbSearch,
                'panier' => $panier,
            ]);
        }

    }

}
