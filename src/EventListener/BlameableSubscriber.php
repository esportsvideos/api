<?php

namespace App\EventListener;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Bundle\SecurityBundle\Security;

#[AsDoctrineListener(event: Events::prePersist, priority: 500, connection: 'default')]
#[AsDoctrineListener(event: Events::preUpdate, priority: 500, connection: 'default')]
readonly class BlameableSubscriber
{
    public function __construct(private Security $security)
    {
    }

    public function prePersist(PrePersistEventArgs $args): void
    {
        $user = $this->getUser();

        if (null === $user) {
            return;
        }

        $entity = $args->getObject();
        if (method_exists($entity, 'setCreatedBy')) {
            $entity->setCreatedBy($user);
        }
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $user = $this->getUser();

        if (null === $user) {
            return;
        }

        $entity = $args->getObject();
        if (method_exists($entity, 'setUpdatedBy')) {
            $entity->setUpdatedBy($user);
        }
    }

    private function getUser(): ?User
    {
        $user = $this->security->getUser();

        return $user instanceof User ? $user : null;
    }
}
