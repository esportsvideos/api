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
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;

/**
 * When we success the authentication, if the user has his token expired set to true, we set it to false.
 *
 * @author Maxime Cornet <xelysion@icloud.com>
 */
class AuthenticationSuccessListener
{
    /** @var EntityManagerInterface $em */
    private $em;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param AuthenticationSuccessEvent $event
     */
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event): void
    {
        /** @var User $user */
        $user = $event->getUser();

        if ($user->isTokenExpired()) {
            $user->setTokenExpired(false);
            $this->em->flush();
        }
    }
}
