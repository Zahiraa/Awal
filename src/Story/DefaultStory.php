<?php

namespace App\Story;

use App\Entity\User;
use App\Factory\UserFactory;
use Zenstruck\Foundry\Story;

final class DefaultStory extends Story
{
    public function build(): void
    {
        // admin
        UserFactory::createOne([
            'roles' => ['ROLE_ADMIN'],
            'email' => 'admin@sfifa.com',
            'statut' => User::ACTIVE,
            'is_verified' => true,
        ]);

        // manager
        UserFactory::createOne([
            'roles' => ['ROLE_MANAGER'],
            'email' => 'manager@sfifa.com',
            'statut' => User::ACTIVE,
            'is_verified' => true,
        ]);

        // user
        UserFactory::createOne([
            'roles' => ['ROLE_USER'],
            'email' => 'user@sfifa.com',
            'statut' => User::ACTIVE,
            'is_verified' => true,
        ]);
    }
}
