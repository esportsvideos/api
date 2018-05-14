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

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * In PUT & POST method, update the plainPassword to encoded password.
 *
 * @author Maxime Cornet <xelysion@icloud.com>
 */
class UpdatePasswordSubscriber implements EventSubscriberInterface
{
    /** @var UserPasswordEncoderInterface $encoder */
    private $encoder;

    /**
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['updatePassword', EventPriorities::PRE_WRITE],
        ];
    }

    /**
     * @param GetResponseForControllerResultEvent $event
     */
    public function updatePassword(GetResponseForControllerResultEvent $event): void
    {
        /** @var User $user */
        $user = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (!$user instanceof User || (Request::METHOD_POST !== $method && Request::METHOD_PUT !== $method)) {
            return;
        }

        $user->setPassword($this->encoder->encodePassword($user, $user->getPlainPassword()));
    }
}
