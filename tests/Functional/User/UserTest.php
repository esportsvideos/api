<?php

namespace App\Tests\Functional\User;

use App\Tests\Functional\ApiTestCase;
use DataFixtures\Data\UserFixtures;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;

#[Group('user')]
final class UserTest extends ApiTestCase
{
    #[Test]
    #[DataProvider('getUserCollectionAccessCases')]
    public function iCanOrCannotGetACollectionOfUsers(?string $userUlid, int $expectedStatusCode): void
    {
        $client = null !== $userUlid
            ? $this->authenticateTestClientAs($userUlid)
            : $this->client;

        $client->request('GET', '/users');

        if (Response::HTTP_OK === $expectedStatusCode) {
            self::assertResponseIsSuccessful();
            self::assertResponseHeaderSame('Content-Type', 'application/ld+json; charset=utf-8');
        } else {
            self::assertResponseStatusCodeSame($expectedStatusCode);
        }
    }

    /**
     * @return iterable<string, mixed>
     */
    public static function getUserCollectionAccessCases(): iterable
    {
        yield 'anonymous cannot access' => [
            'userUlid' => null,
            'expectedStatusCode' => Response::HTTP_UNAUTHORIZED,
        ];

        yield 'user cannot access' => [
            'userUlid' => UserFixtures::USER_ULID,
            'expectedStatusCode' => Response::HTTP_FORBIDDEN,
        ];

        yield 'admin can access' => [
            'userUlid' => UserFixtures::ADMIN_ULID,
            'expectedStatusCode' => Response::HTTP_OK,
        ];
    }

    /**
     * @param array<string, string> $expectedData
     * @param array<string, string> $forbiddenFields
     */
    #[Test]
    #[DataProvider('getUserAccessCases')]
    public function iCanGetAUser(?string $userUlid, array $expectedData = [], array $forbiddenFields = []): void
    {
        $client = null !== $userUlid
            ? $this->authenticateTestClientAs($userUlid)
            : $this->client;

        $response = $client->request('GET', sprintf('/users/%s', UserFixtures::ADMIN_ULID));

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('Content-Type', 'application/ld+json; charset=utf-8');

        $data = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        foreach ($forbiddenFields as $field) {
            self::assertArrayNotHasKey($field, $data);
        }

        self::assertJsonContains($expectedData);
    }

    /**
     * @return iterable<string, mixed>
     */
    public static function getUserAccessCases(): iterable
    {
        yield 'anonymous' => [
            'userUlid' => null,
            'expectedData' => [
                '@context' => '/contexts/User',
                '@type' => 'User',
                'username' => 'admin',
                'country' => 'FR',
            ],
            'forbiddenFields' => ['email', 'password'],
        ];

        yield 'user' => [
            'userUlid' => UserFixtures::USER_ULID,
            'expectedData' => [
                '@context' => '/contexts/User',
                '@type' => 'User',
                'username' => 'admin',
                'country' => 'FR',
            ],
            'forbiddenFields' => ['email', 'password'],
        ];

        yield 'admin' => [
            'userUlid' => UserFixtures::ADMIN_ULID,
            'expectedData' => [
                '@context' => '/contexts/User',
                '@type' => 'User',
                'username' => 'admin',
                'country' => 'FR',
                'email' => 'admin@esports-videos.com',
            ],
            'forbiddenFields' => ['password'],
        ];
    }
}
