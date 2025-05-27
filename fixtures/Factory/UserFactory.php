<?php

namespace DataFixtures\Factory;

use App\Entity\User;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<User>
 */
final class UserFactory extends PersistentProxyObjectFactory
{
    public const string DEFAULT_PASSWORD = 'esv';

    public static function class(): string
    {
        return User::class;
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaults(): array|callable
    {
        return [
            'password' => '$2y$13$i/FkNJCWxC8nTWNqo9qxD.eW.dByex5TlNq1vGQj1MzN9LeBvOV7S',
            'roles' => ['ROLE_USER'],
            'username' => self::faker()->unique()->userName(),
        ];
    }

    public function isAdmin(): self
    {
        return $this->with(['roles' => ['ROLE_ADMIN']]);
    }
}
