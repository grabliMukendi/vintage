<?php

namespace App\Controller;

use App\Events;
use App\Form\ProfilType;
use App\Form\ResetPasswordType;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user")
     */
    public function index()
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    /**
     * @Route("/user/profile/name", name="user_profile_name")
     */
    public function profileNameAction(Request $request) 
    {
        $user = $this->getUser();

        return $this->render('user/profile_name.html.twig', 
        [
            'user' => $user,

        ]);
    }

    /**
     * @Route("/user/profile/", name="user_profile")
     */
    public function profileAction() 
    {
        $user = $this->getUser();

        return $this->render('user/profile.html.twig', 
        [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/user/profile/infos-personnelles", name="user_infos_perso")
     */
    public function resetInfosPersoAction(Request $request, EntityManagerInterface $manager, EventDispatcherInterface $eventDispatcher) 
    {
        $user = $this->getUser();

        $form = $this->createForm(ProfilType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) 
        {
            $manager->persist($user);
            $manager->flush();

            //On déclenche l'eventDispatcher
            //$event = new GenericEvent($user);
            //$eventDispatcher->dispatch(Events::USER_PROFILE_UPDATED, $event);

            $this->addFlash('success', '<strong>' . $user->getPrenom() . '</strong>, vos informations personnelles ont été mis à jour avec succès.' );
            return $this->redirectToRoute('user_profile');
        }

        return $this->render('user/infos_perso.html.twig', 
        [
            'user' => $user,
            'form' => $form->createView(),

        ]);
    }

    /**
     * @Route("/user/profile/reset-address", name="user_reset_address")
     */
    public function resetAdressAction(Request $request, EntityManagerInterface $manager, EventDispatcherInterface $eventDispatcher) 
    {
        $user = $this->getUser();

        $form = $this->createForm(ProfilType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) 
        {
            $manager->persist($user);
            $manager->flush();

            //On déclenche l'eventDispatcher
            //$event = new GenericEvent($user);
            //$eventDispatcher->dispatch(Events::USER_PROFILE_UPDATED, $event);

            $this->addFlash('success', '<strong>' . $user->getPrenom() . '</strong>, vos coordonnées ont été mis à jour avec succès.' );
            return $this->redirectToRoute('user_profile');
        }

        return $this->render('user/reset_address.html.twig', 
        [
            'user' => $user,
            'form' => $form->createView(),

        ]);
    }

    /**
     * @Route("/user/profile/reset-email", name="user_reset_email")
     */
    public function resetEmailAction(Request $request, EntityManagerInterface $manager, EventDispatcherInterface $eventDispatcher) 
    {
        $user = $this->getUser();

        $form = $this->createForm(ProfilType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) 
        {
            $manager->persist($user);
            $manager->flush();
            
            //On déclenche l'eventDispatcher
            //$event = new GenericEvent($user);
            //$eventDispatcher->dispatch(Events::USER_PROFILE_UPDATED, $event);

            $this->addFlash('success', '<strong>' . $user->getPrenom() . '</strong>, votre email a été mis à jour avec succès.' );
            return $this->redirectToRoute('user_profile');
        }

        return $this->render('user/reset_email.html.twig', 
        [
            'user' => $user,
            'form' => $form->createView(),

        ]);
    }

    /**
     * @Route("/user/reset-password/", name="reset_password")
     * Route de modification du mot de passe
     */
    public function resetPasswordAction(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder, EventDispatcherInterface $eventDispatcher) 
    {
        $user = $this->getUser();
        $form = $this->createForm(ResetPasswordType::class, $user);
        $form->handleRequest($request);
        
        //dd($request->get('soumission'));
        if($form->isSubmitted() && $form->isValid()) 
        {
            $oldPassword = $request->request->get('reset_password')['oldPassword'];
            //dd($oldPassword);
            // Si l'ancien mot de passe est bon
            if ($encoder->isPasswordValid($user, $oldPassword)) {
                $newEncodedPassword = $encoder->encodePassword($user, $user->getPlainPassword());
                $user->setPassword($newEncodedPassword);
                
                $manager->persist($user);
                $manager->flush();

                //On déclenche l'eventDispatcher
                //$event = new GenericEvent($user);
                //$eventDispatcher->dispatch(Events::USER_PASSWORD_UPDATED, $event);

                $this->addFlash('success', '<strong>'. $user->getPrenom(). '</strong>, votre mot de passe à bien été changé !');

                return $this->redirectToRoute('user_profile');
            } else {
                $form->addError(new FormError('Ancien mot de passe incorrect'));
            }
        }

        return $this->render('user/reset_password.html.twig', 
        [
            'user' => $user,
            'form' => $form->createView(),
        ]);

    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/image", name="image", methods={"POST"}, options={"expose"=true})
     */
    public function getAvatar(Request $request, EntityManagerInterface $manager) 
    {
        if($request->isXmlHttpRequest()) 
        {
            $user = $this->getUser();
            $avatar = $user->getUserAdress()->getAvatar();
            
            $file = new UploadedFile($_FILES['file']['tmp_name'], $_FILES['file']['name'], $_FILES['file']['type'], $_FILES['file']['error']);
            $file_name = md5(uniqid()) .'.'. $file->guessExtension();

            if(null !== $avatar) 
            {
                $this->removeFile($avatar);

                if($file) 
                {
                    try 
                    {
                        $file->move($this->getTargetDir(), $file_name);
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }
                    // Persist to Database
                    $user->getUserAdress()->setAvatar($file_name);
                    $manager->persist($user);
                    $manager->flush();
                }
            }

            return new JsonResponse($file_name);
        }
        
    }

    private function getTargetDir() 
    {
        return $this->getParameter('avatars_directory');
    }

    private function removeFile($file)
    {
        if($file) 
        {
            $file_path = $this->getTargetDir().'/'.$file;
            if(file_exists($file_path)) unlink($file_path);
        }
    }

}
