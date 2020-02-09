<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AdminAccountController extends AbstractController
{
    /**
     * @Route("/admin/login", name="admin_account_login")
     */
    public function loginAction(AuthenticationUtils $auth)
    {
        $lastUsername = $auth->getLastUsername();

        $errors = $auth->getLastAuthenticationError();

        return $this->render('admin/account/login.html.twig', 
        [
            'hasError' => $errors !== null,
            'username' => $lastUsername,
        ]);
    }
}
