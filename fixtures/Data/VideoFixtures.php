<?php

namespace DataFixtures\Data;

use App\Entity\Video;
use DataFixtures\Factory\UserFactory;
use DataFixtures\Factory\VideoFactory;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\Uid\Ulid;

class VideoFixtures extends AbstractFixture implements DependentFixtureInterface
{
    public const string COMPLETE_ULID = '01973696-f6dc-0905-18d7-0a10fd24b977';

    public function loadSpecificFixtures(): void
    {
        VideoFactory::new(
            [
                'id' => Ulid::fromString(self::COMPLETE_ULID),
                'title' => 'Complete Video',
                'description' => 'All fields contain valid values.',
                'duration' => 156,
                'releaseDate' => new \DateTimeImmutable('now'),
                'createdBy' => UserFactory::find(UserFixtures::ADMIN_ULID),
                'updatedBy' => UserFactory::find(UserFixtures::ADMIN_ULID),
            ]
        )->create();
    }

    public function createRandomEntity(): void
    {
        VideoFactory::createOne();
    }

    public function getFixtureEntityClass(): string
    {
        return Video::class;
    }

    public function getDependencies(): array
    {
        return [UserFixtures::class];
    }
}
