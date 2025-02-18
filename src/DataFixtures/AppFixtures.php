<?php

namespace App\DataFixtures;

use App\Entity\Book;
use App\Entity\Month;
use App\Entity\Profile;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $userPasswordHasher;   

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }
    public function load(ObjectManager $manager): void
    {
          
        for ($i=0; $i < 4 ; $i++)
        { 
            $user = new User();
            $user->setEmail("user$i@api.com");        
            $user->setPassword($this->userPasswordHasher->hashPassword($user, "password"));
            $manager->persist($user);
            for ($ii=0; $ii < 4 ; $ii++)
            {
                $m = new Month();
                $m->setAmount(rand(500,1000));
                $m->setDate(new \DateTime("2024-0$ii-20"));
                $m->setUserid($user);
                $manager->persist($m);
            }
            $p = new Profile();
            $p->setName("name$i");
            $p->setUserid($user);
            $manager->persist($p);
        
        }

        $manager->flush();
    }
}
