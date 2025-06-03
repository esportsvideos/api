<?php

namespace DataFixtures\Factory;

use App\Entity\Video;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Video>
 */
final class VideoFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Video::class;
    }

    /**
     * @return array<string, mixed>
     *
     * @throws \JsonException
     */
    protected function defaults(): array
    {
        return [
            'title' => self::faker()->words(self::faker()->numberBetween(2, 5), true),
            'description' => self::faker()->paragraphs(3, true),
            'duration' => self::faker()->randomNumber(3),
            'releaseDate' => self::faker()->dateTime(),
            'createdBy' => UserFactory::random(),
            'updatedBy' => UserFactory::random(),
        ];
    }
}
