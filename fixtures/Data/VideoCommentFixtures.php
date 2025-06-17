<?php

namespace DataFixtures\Data;

use App\Entity\VideoComment;
use DataFixtures\Factory\UserFactory;
use DataFixtures\Factory\VideoCommentFactory;
use DataFixtures\Factory\VideoFactory;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\Uid\Ulid;

class VideoCommentFixtures extends AbstractFixture implements DependentFixtureInterface
{
    public const string COMMENT_FROM_USER_ULID = '01976990-74be-6ff7-3d7c-b0ea9f351702';
    public const string MODERATED_COMMENT_ULID = '01976453-4432-0a4c-fdf9-85897c60b81d';
    public const string COMMENT_FROM_ANOTHER_USER = '019779c6-bed8-116e-a5d9-0081d6e2edd5';

    public function loadSpecificFixtures(): void
    {
        $video = VideoFactory::find(VideoFixtures::COMPLETE_WITH_SPECIFIC_COMMENTS_ULID);
        $user = UserFactory::find(UserFixtures::USER_ULID);
        $anotherUser = UserFactory::find(UserFixtures::ANOTHER_USER_ULID);
        $admin = UserFactory::find(UserFixtures::ADMIN_ULID);

        VideoCommentFactory::new([
            'id' => Ulid::fromString(self::COMMENT_FROM_USER_ULID),
            'video' => $video,
            'user' => $user,
            'comment' => 'This is a simple comment.',
        ])->create();

        VideoCommentFactory::new([
            'id' => Ulid::fromString(self::MODERATED_COMMENT_ULID),
            'video' => $video,
            'user' => $user,
            'comment' => 'This is a moderated comment.',
            'moderatedBy' => $admin,
            'moderatedAt' => new \DateTimeImmutable('now'),
        ])->create();

        VideoCommentFactory::new([
            'id' => Ulid::fromString(self::COMMENT_FROM_ANOTHER_USER),
            'video' => $video,
            'user' => $anotherUser,
            'comment' => 'This is a simple comment from another user.',
        ])->create();
    }

    public function createRandomEntity(): void
    {
        VideoCommentFactory::new()->moderated()->create();
    }

    public function getFixtureEntityClass(): string
    {
        return VideoComment::class;
    }

    public function getDependencies(): array
    {
        return [VideoFixtures::class];
    }
}
