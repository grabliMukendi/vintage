<?php

namespace App\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use App\Entity\User;

class LoginListener 
{
    private $manager;

    public function __construct(EntityManagerInterface $manager) 
    {
        $this->manager = $manager;
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event) 
    {
        $user = $event->getAuthenticationToken()->getUser();
        //dd($event);
        $user->setLastLogin(new \DateTime);
        $this->manager->persist($user);
        $this->manager->flush();
    }

}