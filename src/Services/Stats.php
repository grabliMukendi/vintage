<?php

namespace App\Services;

use Doctrine\ORM\EntityManagerInterface;

class Stats 
{
    private $manager;

    public function __construct(EntityManagerInterface $manager) 
    {
        $this->manager = $manager;
    }

    public function getStats() 
    {
        $users          = $this->getUsersCount();
        $comments       = $this->getCommentsCount();
        $remises       = $this->getRemisesCount();

        return compact('users', 'comments', 'remises');
    }

    public function getUsersCount() 
    {
        return $this->manager->createQuery('SELECT count(u) FROM App\Entity\User u')->getSingleScalarResult();
    }

    public function getCommentsCount() 
    {
        return $this->manager->createQuery('SELECT count(rating) FROM App\Entity\Comments rating')->getSingleScalarResult();
    }

    public function getRemisesCount() 
    {
        return $this->manager->createQuery('SELECT count(r) FROM App\Entity\Promotion r')->getSingleScalarResult();
    }

}