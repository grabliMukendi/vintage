<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Entity\Produits;
use App\Form\CommentsType;
use App\Services\Paginator;
use App\Services\PaginatorByFilter;
use App\Repository\ProduitsRepository;
use App\Repository\CommandesRepository;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProduitsController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * Route qui mène vers la page d'accueil
     */
    public function index(SessionInterface $session, ProduitsRepository $repo)
    {
        $stringSaisie = null;
        $nbSearch = null;

        return $this->render('produits/index.html.twig', 
        [
            'tendances' => $repo->findCategorieByItem('tendance'),
            'artDecos' => $repo->findCategorieByItem('art déco'),
            'automobiles' => $repo->findCategorieByItem('automobile'),
            'sportLoisirs' => $repo->findCategorieByItem('sport et loisir'),
            'stringSaisie' => $stringSaisie,
            'nbSearch' => $nbSearch,
        ]);
    }

    /**
     * @Route("/produits/{id}/{slug}", name="fiche_produit")
     * Fiche produit
     */
    public function ficheProduit($id, $slug = null, Produits $produit, SessionInterface $session, ProduitsRepository $repo, CategoriesRepository $repoCategorie, Request $request, EntityManagerInterface $manager, CommandesRepository $repoCommandes) 
    {
        $commande = null;

        foreach($repoCommandes->byFacture($this->getUser()) as $key => $value1) 
        {
            foreach($value1->getCommande() as $key2 => $value2) 
            {
                if($key2 == 'produit') $commande = $value2;
            }
        }
        //dd($commande);
        
        if($session->has('panier')) 
        {
            $panier = $session->get('panier');
        } else {
            $panier = false;
        }
        
        $catName = null;
        $catId = null;
        
        foreach($repo->findBy([ 'id' => $id ]) as $key => $value) 
        {
            $catId = $value->getCategorie()->getId();
        }

        foreach($repoCategorie->findBy([ 'id' => $catId ]) as $categorie) 
        {
            $catName = $categorie->getNom();
        }
        //dd($catName);

        $comment = new Comments;
        $form = $this->createForm(CommentsType::class, $comment);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) 
        {
            $comment->setAuthor($this->getUser());
            $comment->setProduit($produit);
            $manager->persist($comment);
            $manager->flush();

            $this->addFlash('success', '<strong>' . $this->getUser()->getPrenom() . '</strong>, nous vous remercions d\'avoir donner votre avis !');
            
            return $this->redirect($request->getUri());
        }


        return $this->render('produits/fiche_produit.html.twig', 
        [
            'user' => $this->getUser(),
            'commandeUser' => $commande,
            'produit' => $produit,
            'panier' => $panier,
            'otherProducts' => $repo->findOtherCategorie($catName, $id),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/categorie/{cat}/{page<\d+>?1}", name="categorie_produits")
     * Affichage par catégorie
     */
    public function produitCategorie($cat, SessionInterface $session, Request $request, $page, Paginator $pagination) 
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

        $categorie = $pagination->findByCategorie($cat);
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

            return $this->render('categorie/products_by_category.html.twig', 
            [
                'produits' => $categorie, // Affichage des produits par catégorie
                'page' => $page,
                'pages' => ceil($total / $pagination->getLimit()),
                'catName' => $cat,
                'stringSaisie' => $stringSaisie,
                'nbSearch' => $nbSearch,
                'panier' => $panier,
            ]);
        }
    }

    /**
     * @Route("/couleur/{color}/{page<\d+>?1}", name="couleur_produits")
     * Affichage par couleur
     */
    public function produitCouleur($color, SessionInterface $session, Request $request, $page, Paginator $pagination) 
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

        $couleurs = $pagination->findByCouleur($color);
        if(!$couleurs) {
            throw $this->createNotFoundException('Aïe! La Page n\'existe pas !');
        }

        if($request->isXmlHttpRequest()) 
        {
            $content = $this->renderView('couleur/couleurs_js_filters.html.twig', 
            [
                'produits' => $couleurs, // Affichage des produits par couleur
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

            return $this->render('couleur/products_by_color.html.twig', 
            [
                'produits' => $couleurs, // Affichage des produits par catégorie
                'page' => $page,
                'pages' => ceil($total / $pagination->getLimit()),
                'colorName' => $color,
                'stringSaisie' => $stringSaisie,
                'nbSearch' => $nbSearch,
                'panier' => $panier,
            ]);
        }
    }

    /**
     * @Route("/taille/{size}/{page<\d+>?1}", name="taille_produits")
     * Affichage par taille
     */
    public function produitTaille($size, SessionInterface $session, Request $request, $page, Paginator $pagination) 
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

        $tailles = $pagination->findByTaille($size);
        if(!$tailles) {
            throw $this->createNotFoundException('Aïe! La Page n\'existe pas !');
        }

        if($request->isXmlHttpRequest()) 
        {
            $content = $this->renderView('taille/tailles_js_filters.html.twig', 
            [
                'produits' => $tailles, // Affichage des produits par taille
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

            return $this->render('taille/products_by_size.html.twig', 
            [
                'produits' => $tailles, // Affichage des produits par catégorie
                'page' => $page,
                'pages' => ceil($total / $pagination->getLimit()),
                'sizeName' => $size,
                'stringSaisie' => $stringSaisie,
                'nbSearch' => $nbSearch,
                'panier' => $panier,
            ]);
        }

            
    }

    /**
     * @Route("/produit/prix-croissant/{page<\d+>?1}", name="produit_prix_croissant")
     */
    public function prixCroissantAction(SessionInterface $session, Request $request, $page, Paginator $pagination) 
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

        if($request->isXmlHttpRequest()) 
        {
            //dd($produits);
            $content = $this->renderView('produits/min_price_js_filters.html.twig', 
            [
                'produits' => $pagination,
                'stringSaisie' => $stringSaisie,
                'nbSearch' => $nbSearch,
                'panier' => $panier,
            ]);

            if(count($pagination->getPrixCroissant()) != 0) 
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
            return $this->render('produits/index.html.twig', 
            [
                'produits' => $pagination, // Affichage des produits par catégorie
                'stringSaisie' => $stringSaisie,
                'nbSearch' => $nbSearch,
                'panier' => $panier,
            ]);
        }
    }

    /**
     * @Route("/produit/prix-decroissant/{page<\d+>?1}", name="produit_prix_decroissant")
     */
    public function prixDecroissantAction(SessionInterface $session, Request $request, $page, Paginator $pagination) 
    {
        $stringSaisie = null;
        $nbSearch = null;
        $pagination ->setEntity(Produits::class)
                    ->setPage($page);

        $stringSaisie = null;
        $nbSearch = null;

        $produits = $repo->prixDecroissant();

        if($session->has('panier')) 
        {
            $panier = $session->get('panier');
        } else {
            $panier = false;
        }

        if($request->isXmlHttpRequest()) 
        {
            $content = $this->renderView('produits/max_price_js_filters.html.twig', 
            [
                'produits' => $pagination,
                'stringSaisie' => $stringSaisie,
                'nbSearch' => $nbSearch,
                'panier' => $panier,
            ]);

            if(count($pagination->getPrixDecroissant()) != 0) 
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
            return $this->render('produits/index.html.twig', 
            [
                'produits' => $pagination, // Affichage des produits par catégorie
                'stringSaisie' => $stringSaisie,
                'nbSearch' => $nbSearch,
                'panier' => $panier,
            ]);
        }
    }

    /**
     * @Route("/rechercher-produits/{page<\d+>?1}", name="search_products")
     * Route pour rechercher les produits
     */
    public function searchAction(ProduitsRepository $repo, Request $request, SessionInterface $session, $page, Paginator $pagination) 
    {
        $pagination ->setEntity(Produits::class)
                    ->setPage($page);
                    
        if($request->isXmlHttpRequest()) 
        {
            $query = preg_replace('#[^a-zA-Z ?0-9]#i', '', $request->get('query'));
            $produits = $repo->searchProducts($query);
            $content =  $this->renderView('produits/search_is_ajax.html.twig',
            [
                'produits' => $produits,
                'stringSaisie' => $query,
                'nbSearch' => count($repo->searchProducts($query)),
            ]);

            //dd($produits);

            if(count($produits) != 0) 
            {
                return new JsonResponse(
                [
                    'success' => 'OK',
                    'results' => $content,
                ], 200);
            } else 
            {
                return new JsonResponse([
                    'errors' => '<strong>Désolé !</strong> Aucun produit ne correspond à <strong>« '. $query .' ».</strong>'
                ]);
            }

        } else 
        {
            if($request->getMethod() == 'POST') {
                $query = preg_replace('#[^a-zA-Z ?0-9]#i', '', $request->get('query')); 
            } else 
            {
                throw $this->createNotFoundException('Aïe! La Page n\'existe pas !');
            }
    
            if($session->has('panier')) 
            {
                $panier = $session->get('panier');
            } else {
                $panier = false;
            }
            //dd($pagination->searchProducts($query));
            return $this->render('produits/search_products.html.twig', 
            [
                'produits' => $pagination->searchProducts($query),
                'stringSaisie' => $query,
                'nbSearch' => count($pagination->searchProducts($query)),
                'panier' => $panier,
            ]);

        }

    }

    /**
     * @Route("/produits/fourchette-prix/", name="show_price")
     */
    public function showPriceAction(ProduitsRepository $repo, Request $request, SessionInterface $session) 
    {
        $price = $request->get('min_price');
        $produits = $repo->getByPrice($price);
        //dd($price);

        if($request->isXmlHttpRequest()) 
        {
            if($session->has('panier')) 
            {
                $panier = $session->get('panier');
            } else {
                $panier = false;
            }
            $content = $this->renderView('produits/show_price.html.twig', 
            [
                'produits' => $produits,
                'panier' => $panier,
            ]);

            //dd($price);
            
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
    
    /* findBy([$criteria], [$order], $limit => integer, $offset) */
}
