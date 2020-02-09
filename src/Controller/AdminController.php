<?php

namespace App\Controller;

use App\Entity\Produits;
use App\Form\ProduitsType;
use App\Services\Paginator;
use App\Repository\ProduitsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin/produit/show/{page<\d+>?1}", name="show_produits")
     * Page Gestion des produits pour l'admin
     */
    public function showProduits(ProduitsRepository $repo, $page, Paginator $pagination) 
    {
        $pagination ->setEntity(Produits::class)
                    ->setPage($page)
                    ->setLimit(6);

        return $this->render('admin/list_produits.html.twig', [
            'produits' => $pagination,
        ]);
    }

    /**
     * @Route("/admin/produit/add", name="produit_add")
     * Page d'ajout des produits dans la BDD
     */
    public function addProduit(Request $request, EntityManagerInterface $manager)
    {
        $produit = new Produits;
        $form = $this->createForm(ProduitsType::class, $produit);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) 
        {
            $images = $produit->getImage();
            foreach($images as $key => $image){
                $image->setProduits($produit);
                $images->set($key,$image);
            }

            $manager->persist($produit);

            $manager->flush();

            $this->addFlash('success', 'Le produit <strong>' . $produit->getTitre() . ',</strong> a été ajouté avec succès.');
            return $this->redirectToRoute('show_produits');
        }

        return $this->render('admin/produit_add_up.html.twig', 
        [
            'produitForm' => $form->createView(),
            'action' => 'Ajouter'
        ]);
    }

    /**
     * @Route("/admin/produits/photos/{id}", name="other_photos")
     * Route d'affichage des autres photos
     */
    public function otherPhotos($id, Produits $produit) 
    {
        return $this->render('produits/other_product_photos.html.twig', 
        [
            'produit' => $produit,
        ]);

    }

    /**
     * @Route("/admin/produit/update/{id}", name="produit_update")
     * Page de modification du produit
     */
    public function updateProduit($id, Request $request, EntityManagerInterface $manager, ProduitsRepository $repo) 
    {
        $produit = $repo->find($id);
        $form = $this->createForm(ProduitsType::class, $produit);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) 
        {
            $images = $produit->getImage();
            foreach($images as $key => $image){
                $image->setProduits($produit);
                $images->set($key,$image);
            }

            if($produit->getPromotion() !== null) 
            {
                if(strlen($produit->getPromotion()->getCodePromo()) < 8) 
                {
                    $produit->getPromotion()->setCodePromo(bin2hex(openssl_random_pseudo_bytes(4)));
                }
            }

            if(empty($produit->getSlug())) $produit->setSlug($produit->getTitre());

            $manager->persist($produit);

            $manager->flush();

            $this->addFlash('success', 'Le produit <strong>' . $produit->getTitre() . ',</strong> a été modifié avec succès.');
            return $this->redirectToRoute('show_produits');
        }

        return $this->render('admin/produit_add_up.html.twig', 
        [
            'produitForm' => $form->createView(),
            'action' => 'Modifier'
        ]);
    }

    /**
     * @Route("/admin/produit/delete/{id}", name="produit_delete")
     * Route de suppression du produit
     */
    public function deleteProduit($id, EntityManagerInterface $manager, ProduitsRepository $repo) 
    {
        $produit = $repo->find($id);

        if($produit) 
        {
            $manager->remove($produit);
            $manager->flush();
            $this->addFlash('success', 'Le produit <strong>' . $produit->getTitre() . '</strong> a été supprimé avec succès.');
        }
        
        return $this->redirectToRoute('show_produits');
    }

}
