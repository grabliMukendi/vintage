<?php

namespace App\Controller;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\Commandes;
use App\Services\Cart\CartService;
use App\Repository\ProduitsRepository;
use App\Repository\CommandesRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserAdressesRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommandesController extends AbstractController
{
    public function prepareCommande(SessionInterface $session, EntityManagerInterface $manager, CommandesRepository $repo, CartService $cartService)
    {
        $user = $this->getUser();
        $commande = null;
        
        if($session->has('commande') === false) {
            $commande = new Commandes;
        } else {
            $commande = $session->get('commande');
        }
        //dd($commande); die;

        if(count($session->get('panier')) == 0) 
        {
            return $this->redirectToRoute('cart_index');
        }
        $commande->setValider(false);
        $commande->setDate(new \DateTime);
        $commande->setUser($user);
        $commande->setReference(0);
        $commande->setCommande($cartService->facture());

        $manager->persist($commande);
        $session->set('commande', $commande);
        
        $manager->flush();
        
        return new Response($commande->getId());
        
    }

    /**
     * @Route("/commandes/list", name="commandes_list")
     */
    public function commandesListAction(CommandesRepository $repo) 
    {
        return $this->render('commandes/commandes_list.html.twig', [
            'commandes' => $repo->byFacture($this->getUser()),
        ]);
    }

    /**
     * @Route("/commandes/facture", name="commandes_facture")
     */
    public function generateFactureAction(CommandesRepository $repo) 
    {
        $facture = $repo->byFacture($this->getUser());
        //dd($facture);

        return $this->render('commandes/commandes_generate_facture.html.twig', [
            'factures' => $facture,
        ]);
    }

    /**
     * @Route("/commandes/facture-pdf/{id}", name="commandes_facture_pdf")
     */
    public function facturePDFAction($id, CommandesRepository $repo) 
    {
        $facture = $repo->findOneBy(
        [ 
            'user' => $this->getUser(), 
            'valider' => 1, 
            'id' => $id, 
        ]);

        if(!$facture) {
            $this->addFlash('errors', '<strong>' . $this->getUser()->getPrenom() . '</strong>, une erreur est survenue ! Veuillez reessayer.');
            return $this->redirectToRoute('commandes_facture');
        }
        
        //Generate pdf with the retrieved HTML
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Courier');
        
        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('commandes/commande_pdf_facture.html.twig', 
        [
            'facture' => $facture,
        ]);
        
        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (inline view)
        $dompdf->stream('facture_'.strtolower($facture->getUser()->getPrenom().'_'.$facture->getUser()->getNom()).'.pdf', 
        [
            'Attachment' => false,
        ]);

    }

    /**
     * @Route("/admin/commandes/registered", name="commandes_registered")
     */
    public function commandesRegisteredAction(CommandesRepository $repo) 
    {
        return $this->render('admin/list_commandes.html.twig', [
            'commandes' => $repo->findAll(),
        ]);
    }

    /**
     * @Route("/admin/commande/delete/{id}", name="commande_delete")
     */
    public function commandesDeleteAction($id, EntityManagerInterface $manager) 
    {
        $commande = $manager->getRepository(Commandes::class)->find($id);
        
        if(null !== $commande) 
        {
            $manager->remove($commande);
            $manager->flush();

            $this->addFlash('success', 'La commande a été supprimée avec succès.');
        }
        return $this->redirectToRoute('commandes_registered');
    }

}
