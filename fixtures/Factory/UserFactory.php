<?php

namespace DataFixtures\Factory;

use App\Entity\User;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<User>
 */
final class UserFactory extends PersistentProxyObjectFactory
{
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
            'password' => '$2y$13$EBvFd9IYD6SxlHJcfZeCrurx96OagZi27E5SH6oxsvQDJFpziUkh6',
            'roles' => ['ROLE_USER'],
            'email' => self::faker()->unique()->email(),
            'username' => self::faker()->unique()->userName(),
            'country' => self::faker()->countryCode(),
            'emailVerified' => true,
        ];
    }

    public function isAdmin(): self
    {
        return $this->with(['roles' => ['ROLE_ADMIN']]);
    }
}
