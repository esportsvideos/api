<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }

        if (false === $user->isEmailVerified()) {
            throw new CustomUserMessageAccountStatusException('Your account is not verified.');
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
    }
}
