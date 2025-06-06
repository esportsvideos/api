<?php

namespace App\Tests\Functional\User;

use App\Tests\Functional\ApiTestCase;
use DataFixtures\Data\UserFixtures;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;

class RegistrationTest extends ApiTestCase
{
    #[Test]
    public function asAnonymousICanRegisterMyself(): void
    {
        $this->client->request('POST', '/users', [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode([
                'email' => 'new_account+alias@esports-videos.com',
                'username' => 'new_account',
                'password' => UserFixtures::DEFAULT_PASSWORD,
                'country' => 'FR',
            ], JSON_THROW_ON_ERROR),
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_CREATED);
        self::assertEmailCount(1);
    }

    #[Test]
    #[DataProvider('invalidData')]
    #[Group('debug')]
    public function asAnonymousICannotRegisterWithInvalidPassword(?string $email, ?string $password, ?string $username, ?string $countryCode, array $violations): void
    {
        $data = array_filter([
            'email' => $email,
            'password' => $password,
            'username' => $username,
            'country' => $countryCode,
        ], static fn ($value) => null !== $value);

        $this->client->request('POST', '/users', [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode($data, JSON_THROW_ON_ERROR),
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        self::assertResponseHeaderSame('content-type', 'application/problem+json; charset=utf-8');
        self::assertJsonContains([
            '@type' => 'ConstraintViolation',
            'title' => 'An error occurred',
            'status' => 422,
            'violations' => $violations,
        ]);
        self::assertEmailCount(0);
    }

    public static function invalidData(): \Generator
    {
        // Empty array
        yield [
            'email' => null,
            'password' => null,
            'countryCode' => null,
            'username' => null,
            'violations' => [],
        ];

        // Email is omitted
        yield [
            'email' => null,
            'password' => UserFixtures::DEFAULT_PASSWORD,
            'countryCode' => 'FR',
            'username' => 'correct_username',
            'violations' => [
                [
                    'propertyPath' => 'email',
                    'message' => 'This value should not be blank.',
                ],
            ],
        ];

        // Password is omitted
        yield [
            'email' => 'correct_email@esports-videos.com',
            'password' => null,
            'countryCode' => 'FR',
            'username' => 'correct_username',
            'violations' => [
                [
                    'propertyPath' => 'password',
                    'message' => 'This value should not be blank.',
                ],
            ],
        ];

        // Invalid Email
        yield [
            'email' => 'invalidEmail',
            'password' => UserFixtures::DEFAULT_PASSWORD,
            'countryCode' => 'FR',
            'username' => 'correct_username',
            'violations' => [
                [
                    'propertyPath' => 'email',
                    'message' => 'This value is not a valid email address.',
                ],
            ],
        ];

        // Invalid Country code
        yield [
            'email' => 'correct_email@esports-videos.com',
            'password' => UserFixtures::DEFAULT_PASSWORD,
            'countryCode' => 'BAD',
            'username' => 'correct_username',
            'violations' => [
                [
                    'propertyPath' => 'country',
                    'message' => 'This value is not a valid country.',
                ],
            ],
        ];

        // Invalid Password
        yield [
            'email' => 'correct_email@esports-videos.com',
            'password' => 'badPassword',
            'countryCode' => 'FR',
            'username' => 'correct_username',
            'violations' => [
                [
                    'propertyPath' => 'password',
                    'message' => 'The password strength is too low. Please use a stronger password.',
                ],
            ],
        ];
    }
}
