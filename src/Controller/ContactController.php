<?php

namespace App\Controller;

use App\Events;
use App\Entity\Contact;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\EventDispatcher\GenericEvent;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="contact")
     * @Security("is_granted('ROLE_USER')")
     */
    public function contactAction(Request $request, EventDispatcherInterface $eventDispatcher, EntityManagerInterface $manager)
    {
        $user = $this->getUser();
        $contact = new Contact;
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);
        $telephone = ($user->getUserAdress() != null) ? $user->getUserAdress()->getTelephone() : '';
        
        if($form->isSubmitted() && $form->isValid()) 
        {
            $manager->persist($contact);
            $manager->flush();

            //On déclenche l'eventDispatcher
            $event = new GenericEvent($user);
            $eventDispatcher->dispatch(Events::USER_CONTACT, $event);

            $this->addFlash('success', '<strong>' . $user->getPrenom() . '</strong>, nous vous remercions de nous avoir contacter.');
            return $this->redirectToRoute('home');
        }
        
        return $this->render('contact/contact.html.twig', 
        [
            'user' => $user,
            'telephone' => $telephone,
            'form' => $form->createView(),
        ]);
    }
}
