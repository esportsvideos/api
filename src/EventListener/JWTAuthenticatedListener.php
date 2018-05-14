<?php

/*
 * This file is part of the Esports Videos project.
 *
 * (c) Esports Videos <https://github.com/esportsvideos/api/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTAuthenticatedEvent;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CredentialsExpiredException;

/**
 * If the user has tokenExpired to true, then throw CredentialsExpiredException.
 *
 * @author Maxime Cornet <xelysion@icloud.com>
 */
class JWTAuthenticatedListener
{
    /**
     * @param JWTAuthenticatedEvent $event
     *
     * @throws CredentialsExpiredException
     */
    public function onJWTAuthenticated(JWTAuthenticatedEvent $event): void
    {
        $user = $this->getUser($event->getToken());

        if (!$user) {
            return;
        }

        if ($user->isTokenExpired()) {
            throw new CredentialsExpiredException();
        }
    }

    /**
     * Get the current user.
     *
     * @param TokenInterface $token
     *
     * @return User|null
     */
    private function getUser(TokenInterface $token): ?User
    {
        return ($token->getUser() instanceof User) ? $token->getUser() : null;
    }
}
