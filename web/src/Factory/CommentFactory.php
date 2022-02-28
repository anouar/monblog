<?php

namespace App\Factory;

use App\Entity\Comment;
use Faker\Factory;

final class CommentFactory extends ModelFactory
{
    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }
    public function published(): self
    {
        return $this->addState(['isPublished' => true]);
    }

    public function commentComment(): self
    {
        return $this->addState([
            'description' =>   $this->faker->realText(200)
        ]);
    }

    protected function getDefaults(): array
    {
        return [
            'content' => 'At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores',
            'createdAt' => new \DateTime(),
            'user' => UserFactory::new(),
            'post' => 1
        ];
    }
}
