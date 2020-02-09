<?php

namespace App\Controller;

use App\Form\LivraisonType;
use App\Entity\UserAdresses;
use App\Services\Cart\CartService;
use App\Repository\ProduitsRepository;
use App\Repository\CommandesRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserAdressesRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{
    /**
     * @Route("/panier", name="cart_index")
     */
    public function panierIndex(CartService $cartService)
    {

        return $this->render('cart/cart.html.twig', 
        [
            'items' => $cartService->getFullCart(),
            'total' => $cartService->getTotal(),
        ]);
    }
    /**
     * @Route("/panier/nombre-articles", name="cart_nb_articles")
     */
    public function panierNbArticles(CartService $cartService)
    {
        return $this->render('cart/cart_nb_articles.html.twig', 
        [
            'nbArticles' => $cartService->getNbProduct(),
        ]);
    }

    /**
     * @Route("/panier/add/{id}", name="cart_add")
     * Route d'ajout du panier
     */
    public function panierAdd($id, CartService $cartService) 
    {
        $cartService->add($id);

        return $this->redirectToRoute('cart_index');

    }

    /**
     * @Route("/panier/addForm/{id}", name="cart_add_in_form")
     * Route d'ajout du panier dans la page Fiche produit
     */
    public function panierAddForm($id, SessionInterface $session, Request $request) 
    {
        $panier = $session->get('panier', []);
        $quantite = $request->get('quantite');
        //dd($quantite);

        if(!empty($panier[$id])) 
        {
            $panier[$id]++;
        } else 
        {
            $panier[$id] = $quantite;
        }
        
        $session->set('panier', $panier);

        return $this->redirectToRoute('cart_index');

    }

    /**
     * @Route("/panier/soustraire/{id}", name="cart_subtracted")
     * Route de retrait de la quantité du produit
     */
    public function panierSubtracted($id, CartService $cartService) 
    {
        $cartService->subtractCart($id);

        return $this->redirectToRoute('cart_index');

    }

    /**
     * @Route("/panier/remove/{id}", name="cart_remove")
     * Route de suppression du panier
     */
    public function panierRemove($id, CartService $cartService) 
    {
        $cartService->remove($id);

        $this->addFlash('success', 'Le produit a été supprimé avec succès.');

        return $this->redirectToRoute('cart_index');
    }

    /**
     * @Route("/panier/livraison/", name="cart_livraison")
     * @Security("is_granted('ROLE_USER')")
     */
    public function livraisonAction(Request $request, EntityManagerInterface $manager) 
    {
        $user = $this->getUser();
        $entity = (empty($user->getUserAdress())) ? new UserAdresses : $user->getUserAdress();
        
        $form = $this->createForm(LivraisonType::class, $entity);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) 
        {
            $entity->setUser($user);
            $manager->persist($entity);
            $manager->flush();

            $this->addFlash('success', '<strong>' . $user->getPrenom() . '</strong>, votre adresse a été mise à jour.');
            return $this->redirectToRoute('cart_livraison');
        }
        
        return $this->render('cart/livraison.html.twig', 
        [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    /**
     * @Route("/panier/livraison/delete-adress/{id}", name="adresse_delete")
     * @Security("is_granted('ROLE_USER')")
     */
    public function adresseDeleteAction($id, UserAdressesRepository $repo, EntityManagerInterface $manager) 
    {
        $user = $this->getUser();
        $adresse = $repo->find($id);
        
        if($adresse) {
            $adresse->setNom(null);
            $adresse->setPrenom(null);
            $adresse->setAdresse(null);
            $adresse->setCodePostal(null);
            $adresse->setPays(null);
            $adresse->setVille(null);
            $adresse->setComplement(null);
            $manager->flush();

            $this->addFlash('success', '<strong>' . $user->getPrenom() . '</strong>, votre adresse a été supprimée avec succès.');
            
            return $this->redirectToRoute('cart_livraison');
        }

        return $this->render('cart/livraison.html.twig', 
        [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/panier/validation/", name="cart_validation")
     * @Security("is_granted('ROLE_USER')")
     */
    public function validationAction(SessionInterface $session, Request $request,UserAdressesRepository $repo, CommandesRepository $repoCommandes) 
    {
        if(!$session->has('adresse')) $session->set('adresse', []);
        $adresse = $session->get('adresse');
    
        $adresse['livraison'] = $repo->findBy([ 'id' => $request->get('livraison') ]);
        
        $session->set('adresse', $adresse);

        $prepareCommande = $this->forward('App\Controller\CommandesController::prepareCommande');
        $commande = $repoCommandes->find($prepareCommande->getContent());
        
        //dd($commande);

        return $this->render('cart/validation.html.twig', 
        [
            'commandes' => $commande,
        ]);
    }

     /**
     * @Route("/panier/validation-commande/{id}", name="commande_validation")
     * @Security("is_granted('ROLE_USER')")
     * Cette méthode remplace l'API Banque
     */
    public function serviceValidationAction($id, Request $request, SessionInterface $session, CommandesRepository $repo, EntityManagerInterface $manager, CartService $cartService, ProduitsRepository $productRepo) 
    {
        $commande = $repo->find($id);

        if(!$commande || $commande->getValider() == true) 
        {
            throw $this->createNotFoundException('Aie !!! La commande n\'existe pas !');
        } else {
            $commande->setValider(true);
            $commande->setReference($cartService->getReference()); // service
            
            /** 
             * Validateur de la commande en raison de la disponibilité du produit  */
            foreach($commande as $indice) 
            {
                $commande = $indice;
            }
            $idProduct = null;
            $quantite = null;
            
            foreach($commande->getCommande() as $key => $value) 
            {
                if($key === 'produit') 
                {
                    foreach($value as $cles => $prod) 
                    {
                        $idProduct = $cles;
                        $quantite = $prod['quantite'];
                    }
                }
            }

            $product = $productRepo->find($idProduct);
            $dispo = $product->getDisponible();
            $plural = $dispo > 1 ? 's' : '';

            if($quantite > $product->getDisponible()) 
            {
                $this->addFlash('errors', 'Désolé '. $this->getUser()->getPrenom() .' ! Le produit <strong>' . $product->getTitre() . ',</strong> a été victime de son succès ('.$dispo.' produit'.$plural.' disponible'.$plural.').');

                return $this->redirectToRoute('cart_livraison');
            } else {
                $manager->flush();
            }
            /**  Fin Validateur */

            $session->remove('adresse');
            $session->remove('panier');
            $session->remove('commande');

            $this->addFlash('success', '<strong>' . $this->getUser()->getPrenom() . '</strong>, votre commande a été validée avec succès.');
        }

        /** 
         * Mise à jour de la disponibilité du produit
        */
        if($commande->getValider() != null) 
        {
            $stockUpdated = $repo->byFacture($this->getUser());

            if($stockUpdated != null) {

                foreach($stockUpdated as $indice) 
                {
                    $stockUpdated = $indice;
                }
                $idProduct = null;
                $quantite = null;
        
                if($stockUpdated->getValider() != null) 
                {
                    foreach($stockUpdated->getCommande() as $key => $value) 
                    {
                        if($key === 'produit') 
                        {
                            foreach($value as $cles => $prod) 
                            {
                                $idProduct = $cles;
                                $quantite = $prod['quantite'];
                            }
                        }
                    }
                }

                if($idProduct != null) 
                {
                    $product = $productRepo->find($idProduct);
                    $product->setDisponible($product->getDisponible() - $quantite);
                    $manager->flush();
                }
            }
        }
        /**  Fin mise à jour */

        return $this->redirectToRoute('commandes_facture');

    }
    
    /**
     * @Route("/factures/list/", name="factures_list")
     * @Security("is_granted('ROLE_USER')")
     */
    public function facturationCommandeAction(CommandesRepository $repo) 
    {
        $facture = $repo->byFacture($this->getUser());

        return $this->render('commandes/list_factures.html.twig', [
            'factures' => $facture,
        ]);
    }

    
}
