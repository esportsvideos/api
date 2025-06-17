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
    public const string COMPLETE_WITH_SPECIFIC_COMMENTS_ULID = '01975f6a-d76c-a6dd-fe26-10c5d1e87006';
    public const string COMPLETE_WITH_A_LOT_OF_COMMENTS_ULID = '01976b8d-ce5b-f7dc-3393-a4f9fd541350';
    public const string UPDATED_VIDEO_ULID = '01977ed2-f6dc-ba27-9e7b-e601124e60d5';

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
            ]
        )->create();

        VideoFactory::new(
            [
                'id' => Ulid::fromString(self::COMPLETE_WITH_SPECIFIC_COMMENTS_ULID),
                'title' => 'Video with specific comments',
                'description' => 'Simple comment, moderated comment.',
                'duration' => 156,
                'releaseDate' => new \DateTimeImmutable('now'),
                'createdBy' => UserFactory::find(UserFixtures::ADMIN_ULID),
            ]
        )->create();

        VideoFactory::new(
            [
                'id' => Ulid::fromString(self::COMPLETE_WITH_A_LOT_OF_COMMENTS_ULID),
                'title' => 'Video with a lot of comments',
                'description' => 'Video with a lot of comments',
                'duration' => 156,
                'releaseDate' => new \DateTimeImmutable('now'),
                'createdBy' => UserFactory::find(UserFixtures::ADMIN_ULID),
            ]
        )->create();

        VideoFactory::new(
            [
                'id' => Ulid::fromString(self::UPDATED_VIDEO_ULID),
                'title' => 'Updated video',
                'description' => 'Simple updated video.',
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
