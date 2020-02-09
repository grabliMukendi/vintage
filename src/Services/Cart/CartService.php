<?php

namespace App\Services\Cart;

use App\Repository\ProduitsRepository;
use App\Repository\CommandesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class CartService 
{
    protected $session;
    protected $produitRepo;
    protected $commandesRepo;
    protected $autorisationChecker;
    protected $manager;

    public function __construct(SessionInterface $session, ProduitsRepository $produitRepo, AuthorizationCheckerInterface $autorisationChecker, EntityManagerInterface $manager, CommandesRepository $commandesRepo) 
    {
        $this->session = $session;
        $this->produitRepo = $produitRepo;
        $this->commandesRepo = $commandesRepo;
        $this->autorisationChecker = $autorisationChecker;
        $this->manager = $manager;
    }

    public function add(int $id) 
    {
        $panier = $this->session->get('panier', []);
        
        //$panier = array();
        if(!empty($panier[$id])) 
        {
            $panier[$id]++;
        } else 
        {
            $panier[$id] = 1;
        }
        
        $this->session->set('panier', $panier);
        
        //dd($session->get('panier'));
    }

    public function subtractCart(int $id) 
    {
        $panier = $this->session->get('panier', []);
        
        //$panier = array();
        if(!empty($panier[$id])) 
        {
            $panier[$id]--;
        } 
        if($panier[$id] == 0) 
        {
            unset($panier[$id]);
        }
        
        $this->session->set('panier', $panier);
    }

    public function remove(int $id) 
    {
        $panier = $this->session->get('panier', []);

        if(!empty($panier[$id])) 
        {
            unset($panier[$id]);
        }
        $this->session->set('panier', $panier); // Cette ligne sert à dire que le panier que je viens de supprimer c'est $panier.
    }

    public function getFullCart() : array 
    {
        $panier = $this->session->get('panier', []);

        $panierWithData = array();

        foreach($panier as $id => $quantity) 
        {
            $panierWithData[] = [
                'produit' => $this->produitRepo->find($id),
                'quantite' => $quantity
            ];
        }
        return $panierWithData;
    }

    public function getNbProduct() : int 
    {
        $panier = $this->session->get('panier', []);

        $panierCountQuantity = array();
        $nbArticles = 0;

        foreach($panier as $quantity) 
        {
            $panierCountQuantity[] = [
                'quantite' => $quantity
            ];
        }
        if($panierCountQuantity) 
        {
            foreach($panierCountQuantity as $articles) {
                $nbArticles += $articles['quantite'];
            }
        }
        return $nbArticles;
    }

    public function getTotal() : float 
    {
        $total = 0;

        foreach($this->getFullCart() as $item) 
        {
            $total+= $item['produit']->getPrix() * $item['quantite'];
        }
        return $total;
    }
    
    public function facture() 
    {
        $adresse = $this->session->get('adresse');
        $panier = $this->session->get('panier');
        $commande = array();
        $totalHT = 0;
        $totalTTC = 0;

        $produits = array();
        foreach(array_keys($this->session->get('panier')) as $prod) {
            $produits[] = $this->produitRepo->find($prod);
        }

        foreach($produits as $produit) 
        {
            $prixHT = ($produit->getPrix() * $panier[$produit->getId()/*quantité*/]);
            $prixTTC = ($prixHT * $produit->getTva()->getMultiplicate());
            $totalHT += $prixHT;
            $totalTTC += $prixTTC;

            if(!isset($commande['tva'][$produit->getTva()->getValeur() . '%'])) 
            {
                $commande['tva'][$produit->getTva()->getValeur() . '%'] = round($totalTTC - $totalHT, 2);
            } else {
                $commande['tva'][$produit->getTva()->getValeur() . '%'] += round($totalTTC - $totalHT, 2);
            }

            $commande['produit'] = array();
            
            $commande['produit'][$produit->getId()] = 
            [
                'reference' => $produit->getTitre(),
                'quantite' => $panier[$produit->getId()],
                'image' => $produit->getImage(),
                'stock' => $produit->getDisponible(),
                'prixHT' => round($produit->getPrix(), 2),
                'prixTTC' => round($produit->getPrix() * $produit->getTva()->getMultiplicate(), 2),
            ];  
        }
        
        $commande['livraison'] = array();
        foreach($adresse as $value) 
        {
            foreach($value as $adr) 
            {
                $commande['livraison'] = 
                [
                    'nom' => $adr->getNom(),
                    'prenom' => $adr->getPrenom(),
                    'telephone' => $adr->getTelephone(),
                    'adresse' => $adr->getAdresse(),
                    'codePostal' => $adr->getCodePostal(),
                    'ville' => $adr->getVille(),
                    'pays' => $adr->getPays(),
                    'complement' => $adr->getComplement(),
                ];
            }
        }

        $commande['totalHT'] = round($totalHT, 2);
        $commande['totalTTC'] = round($totalTTC, 2);
        $commande['token'] = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');

        return $commande;

    }   

    public function getReference() 
    {
        $reference = $this->commandesRepo->findOneBy([ 'valider' => 1 ], [ 'id' => 'DESC' ], 1, 1);

        if(!$reference) 
        {
            return 1;
        } else {
            return $reference->getReference() + 1;
        }
        
    }

}