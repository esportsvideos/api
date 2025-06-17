<?php

namespace DataFixtures\Factory;

use App\Entity\VideoComment;
use DataFixtures\Data\UserFixtures;
use DataFixtures\Data\VideoFixtures;
use Symfony\Component\Uid\Ulid;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<VideoComment>
 */
final class VideoCommentFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return VideoComment::class;
    }

    /**
     * @return array<string, mixed>
     *
     * @throws \JsonException
     */
    protected function defaults(): array
    {
        return [
            'id' => Ulid::fromString(Ulid::generate()),
            'user' => UserFactory::random(),
            'video' => self::faker()->boolean(70) ? VideoFactory::find(VideoFixtures::COMPLETE_WITH_A_LOT_OF_COMMENTS_ULID) : VideoFactory::random(),
            'comment' => self::faker()->realText(),
        ];
    }

    protected function initialize(): static
    {
        return $this
            ->afterPersist(function (VideoComment $videoComment) {
                $videoComment->setCreatedAt(\DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('-10 years')));
            })
        ;
    }

    public function moderated(): static
    {
        return $this
            ->afterInstantiate(function (VideoComment $videoComment) {
                if (self::faker()->boolean(10)) {
                    $videoComment->moderatedAt = new \DateTimeImmutable();
                    $videoComment->moderatedBy = UserFactory::find(UserFixtures::ADMIN_ULID)->_real();
                }
            });
    }
}
