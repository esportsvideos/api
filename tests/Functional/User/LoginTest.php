<?php

namespace App\Tests\Functional\User;

use App\Tests\Functional\ApiTestCase;
use DataFixtures\Data\UserFixtures;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;

#[Group('login')]
#[Group('user')]
class LoginTest extends ApiTestCase
{
    #[Test]
    public function iCantLoginWithUnverifiedAccount(): void
    {
        $this->client->request('POST', '/login_check',
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'body' => json_encode(
                    [
                        'email' => 'unverified@esports-videos.com',
                        'password' => UserFixtures::DEFAULT_PASSWORD,
                    ],
                    JSON_THROW_ON_ERROR
                ),
            ]
        );

        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        self::assertJsonContains(['message' => 'Your account is not verified.']);
    }

    #[Test]
    public function iCanLoginWithVerifiedAccount(): void
    {
        $response = $this->client->request('POST', '/login_check',
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'body' => json_encode(
                    [
                        'email' => 'user@esports-videos.com',
                        'password' => UserFixtures::DEFAULT_PASSWORD],
                    JSON_THROW_ON_ERROR
                ),
            ]
        );

        self::assertResponseIsSuccessful();
        self::assertArrayHasKey('token', $response->toArray());
    }
}
