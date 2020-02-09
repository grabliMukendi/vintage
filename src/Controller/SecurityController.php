<?php

namespace App\Controller;

use App\Events;
use App\Entity\User;
use App\Entity\UserAdresses;
use App\Form\InscriptionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/inscription", name="inscription")
     * Page d'inscription des utilisateurs
     */
    public function inscription(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder, EventDispatcherInterface $eventDispatcher )
    {
        $user = new User;
        $adresse = new UserAdresses;
        $form = $this->createForm(InscriptionType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) 
        {
            $user->setPassword($encoder->encodePassword($user, $user->getPassword()));

            $user->setUserAdress($adresse->setAvatar('default_avatar'));

            $manager->persist($adresse);
            $manager->persist($user);
            $manager->flush();

            //On déclenche l'eventDispatcher
            //$event = new GenericEvent($user);
            //$eventDispatcher->dispatch(Events::USER_REGISTERED, $event);

            $this->addFlash('success', '<strong>' . $user->getPrenom() . '</strong>, votre inscription a réussi avec succès.');
            return $this->redirectToRoute('connexion');
        }

        return $this->render('security/inscription.html.twig', [
            'registerForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/connexion", name="connexion")
     * Page de connexion
     */
    public function connexion(AuthenticationUtils $auth, Request $request) 
    {
        $lastUsername = $auth->getLastUsername();

        $errors = $auth->getLastAuthenticationError();

        return $this->render('security/connexion.html.twig', 
        [
            'hasError' => $errors !== null,
            'username' => $lastUsername,
        ]);
    }

    /**
     * @Route("/deconnexion", name="deconnexion")
     * Page de deconnexion
     */
    public function deconnexion(){}
    
}
