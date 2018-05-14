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

namespace App\Entity;

use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

/**
 * @author Maxime Cornet <xelysion@icloud.com>
 */
class User implements AdvancedUserInterface, \Serializable
{
    private const ROLE_DEFAULT = 'ROLE_USER';

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $plainPassword;

    /**
     * @var string
     */
    private $email;

    /**
     * @var bool
     */
    private $enabled;

    /**
     * @var string
     */
    private $slug;

    /**
     * @var string[]
     */
    private $roles;

    /**
     * @var bool
     */
    private $tokenExpired;

    public function __construct()
    {
        $this->enabled = false;
        $this->tokenExpired = false;
        $this->roles = [self::ROLE_DEFAULT];
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize(
            [
                $this->id,
                $this->username,
                $this->email,
                $this->enabled,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized): void
    {
        [
            $this->id,
            $this->username,
            $this->email,
            $this->enabled,
        ] = unserialize($serialized, ['allowed_classes' => false]);
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    /**
     * {@inheritdoc}
     */
    public function isAccountNonExpired(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isAccountNonLocked(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isCredentialsNonExpired(): bool
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * @return null|string
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return array_unique($this->roles);
    }

    /**
     * @param array $roles
     */
    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    /**
     * @param string $role
     *
     * @return $this
     */
    public function addRole(string $role): self
    {
        $role = strtoupper($role);

        if (!\in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    /**
     * @param string $role
     *
     * @return bool
     */
    public function hasRole(string $role): bool
    {
        return \in_array(strtoupper($role), $this->getRoles(), true);
    }

    /**
     * @return null|string
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    /**
     * @param string $plainPassword
     */
    public function setPlainPassword(string $plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    /**
     * @return bool
     */
    public function isTokenExpired(): bool
    {
        return $this->tokenExpired;
    }

    /**
     * @param bool $tokenExpired
     */
    public function setTokenExpired(bool $tokenExpired): void
    {
        $this->tokenExpired = $tokenExpired;
    }

    /**
     * @param PreUpdateEventArgs $event
     */
    public function preUpdate(PreUpdateEventArgs $event)
    {
        if ($event->hasChangedField('password')) {
            $this->setTokenExpired(true);
        }
    }
}
