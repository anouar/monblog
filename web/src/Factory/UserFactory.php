<?php

namespace App\Factory;

use App\Entity\User;
use Faker\Factory;

final class UserFactory extends ModelFactory
{
    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }

    protected function getDefaults(): array
    {
        return [
            'email' => $this->faker->email,
            'username' => $this->faker->userName,
        ];
    }
}
