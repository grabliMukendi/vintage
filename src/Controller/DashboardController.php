<?php

namespace App\Controller;

use App\Services\Stats;
use App\Repository\CommentsRepository;
use App\Repository\ProduitsRepository;
use App\Repository\CommandesRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DashboardController extends AbstractController
{
    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function index(Stats $statsService, CommentsRepository $commentsRepo, ProduitsRepository $productsRepo, CommandesRepository $commandesRepo)
    {
        return $this->render('admin/dashboard/index.html.twig', [
            'stats' => $statsService->getStats(),
            'products' => $productsRepo->getProductsCount(),
            'commandes' => $commandesRepo->getCommandesCount(),
            'ratings' => $commentsRepo->getRatingsCount(),
            //'bestSales' => $commandesRepo->bestSales(5),
        ]);
    }

    /**
     * Cette méthode permet de calculer la moyenne de la meilleur vente
     * @Route("/dashboard/best-sales")
     * @return float
     */
    public function getBestSales(CommandesRepository $commandeRepo) 
    {
        $commandes = $commandeRepo->getByCommande();
        foreach($commandes as $key => $commande) 
        {
            if($key === 'produit') $product = $commande;
        }

        dd($commandes); die;
        return new Response($commande);
    }

}
