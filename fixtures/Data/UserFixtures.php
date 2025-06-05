<?php

namespace DataFixtures\Data;

use App\Entity\User;
use DataFixtures\Factory\UserFactory;
use Symfony\Component\Uid\Ulid;

class UserFixtures extends AbstractFixture
{
    public const string ADMIN_ULID = '019712ef-fb77-347f-1cd9-2b1c22d259e6';
    public const string USER_ULID = '019712ef-fb78-602c-a546-f7c694bd83be';
    public const string UNVERIFIED_USER_ULID = '01973bad-1197-b299-2e4d-349108befb29';

    public function loadSpecificFixtures(): void
    {
        UserFactory::new([
            'id' => Ulid::fromString(self::ADMIN_ULID),
            'email' => 'admin@esports-videos.com',
            'username' => 'admin',
            'country' => 'FR',
        ])->isAdmin()->create();

        UserFactory::new([
            'id' => Ulid::fromString(self::USER_ULID),
            'email' => 'user@esports-videos.com',
            'username' => 'user',
            'country' => 'FR',
        ])->create();

        UserFactory::new([
            'id' => Ulid::fromString(self::UNVERIFIED_USER_ULID),
            'email' => 'unverified@esports-videos.com',
            'username' => 'unverified.user',
            'country' => 'FR',
            'emailVerified' => false,
        ])->create();
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
