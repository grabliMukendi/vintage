<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Comments;
use App\Repository\UserRepository;
use App\Repository\ProduitsRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    protected $produitRepo;
    protected $userRepo;

    public function __construct(ProduitsRepository $produitRepo, UserRepository $userRepo) 
    {
        $this->produitRepo = $produitRepo;
        $this->userRepo = $userRepo;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('FR-fr');
        $product = $this->produitRepo->findAll();
        $user = $this->userRepo->findAll();
        
        for($i = 0; $i < 15; $i++) 
        {
            if(mt_rand(0, 1)) 
            {
                $comment = new Comments;

                $comment->setContent($faker->paragraph())
                        ->setRating(mt_rand(0, 5))
                        ->setAuthor($user)
                        ->setProduit($product);
        
                $manager->persist($comment);
            }
        }

        $manager->flush();
    }
    
}
