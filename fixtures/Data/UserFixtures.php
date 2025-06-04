<?php

namespace DataFixtures\Data;

use App\Entity\User;
use DataFixtures\Factory\UserFactory;
use Symfony\Component\Uid\Ulid;

class UserFixtures extends AbstractFixture
{
    public const string ADMIN_ULID = '019712ef-fb77-347f-1cd9-2b1c22d259e6';
    public const string USER_ULID = '019712ef-fb78-602c-a546-f7c694bd83be';

    public function loadSpecificFixtures(): void
    {
        UserFactory::new(['id' => Ulid::fromString(self::ADMIN_ULID), 'email' => 'admin@esports-videos.com'])->isAdmin()->create();
        UserFactory::new(['id' => Ulid::fromString(self::USER_ULID), 'email' => 'user@esports-videos.com'])->isAdmin()->create();
    }

    public function createRandomEntity(): void
    {
        UserFactory::createOne();
    }

    public function getFixtureEntityClass(): string
    {
        return User::class;
    }
}
