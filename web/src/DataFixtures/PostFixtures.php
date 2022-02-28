<?php

namespace App\DataFixtures;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PostFixtures extends Fixture
{
    protected $faker;
    protected $encoder;
    public function __construct(UserPasswordHasherInterface $encoder)
    {
        $this->faker = Factory::create('fr_FR');
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $users = [];
        for ($i = 0; $i < 4; $i++) {
            $users[$i] = new User();
            $users[$i]->setFirstname($this->faker->lastName);
            $users[$i]->setLastname($this->faker->firstName);
            $users[$i]->setUsername($this->faker->email);
            $users[$i]->setEmail($this->faker->email);
            $users[$i]->setAvatar("https://placeimg.com/60/60/people/sepia");
            $users[$i]->setRoles(['ROLE_USER']);
            $users[$i]->setPassword($this->encoder->hashPassword(
                $users[$i],
                'test'
            ));
            $manager->persist($users[$i]);
        }

        $manager->flush();


        $posts = [];
        for ($i = 0; $i < 100; $i++) {
            $posts[$i] = new Post();
            $posts[$i]->setTitle($this->faker->realText(100));
            $posts[$i]->setUser($users[$i % 3]);
            $posts[$i]->setContent($this->faker->realText(1500));
            $posts[$i]->setImage("https://picsum.photos/640/360");
            $posts[$i]->setCreatedAt($this->faker->dateTimeBetween('-100 days', '-1 days'));
            $posts[$i]->setPublishedAt($this->faker->dateTimeBetween('-100 days', '-1 days'));
            $posts[$i]->setUpdatedAt($this->faker->dateTimeBetween('-100 days', '-1 days'));
            $posts[$i]->setPublished(true);

            $manager->persist($posts[$i]);
        }

        $manager->flush();
    }
}
