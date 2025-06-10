<?php

namespace App\Tests\Smoke;

use DataFixtures\Data\UserFixtures;
use DataFixtures\Data\VideoFixtures;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApplicationAvailabilityFunctionalTest extends WebTestCase
{
    #[DataProvider('urlProvider')]
    public function testPageIsSuccessful(string $url): void
    {
        $client = self::createClient();
        $client->request('GET', $url);

        self::assertNotSame(500, $client->getResponse()->getStatusCode());
    }

    public static function urlProvider(): \Generator
    {
        yield ['/users'];
        yield [sprintf('/users/%s', UserFixtures::USER_ULID)];
        yield ['/videos'];
        yield [sprintf('/videos/%s', VideoFixtures::COMPLETE_ULID)];
    }
}
