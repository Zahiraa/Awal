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
            'email' => 'admin@awal.com',
            'statut' => User::ACTIVE,
            'is_verified' => true,
        ]);

    }
}
