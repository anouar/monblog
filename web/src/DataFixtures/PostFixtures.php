<?php

namespace App\DataFixtures;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class PostFixtures extends Fixture
{
    protected $faker;

    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
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
