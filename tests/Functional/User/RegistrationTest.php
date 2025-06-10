<?php

namespace App\Tests\Functional\User;

use App\Service\Registration\GenerateSignedUriService;
use App\Tests\Functional\ApiTestCase;
use DataFixtures\Data\UserFixtures;
use DataFixtures\Factory\UserFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Clock\MockClock;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\UriSigner;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RegistrationTest extends ApiTestCase
{
    #[Test]
    public function asAnonymousICanRegisterMyself(): void
    {
        $this->register([
            'email' => 'new_account+alias@esports-videos.com',
            'username' => 'new_account',
            'password' => UserFixtures::DEFAULT_PASSWORD,
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_CREATED);
        self::assertEmailCount(1);
    }

    /**
     * @param array<string, string> $violations
     */
    #[Test]
    #[DataProvider('invalidData')]
    public function asAnonymousICannotRegisterWithInvalidData(?string $email, ?string $password, ?string $username, ?string $countryCode, array $violations = []): void
    {
        $data = array_filter([
            'email' => $email,
            'password' => $password,
            'username' => $username,
            'country' => $countryCode,
        ], static fn ($value) => null !== $value);

        $this->register($data);

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

    #[Test]
    public function asAnonymousICanVerifyMyAccount(): void
    {
        $container = static::getContainer();
        $generateSignedUriService = $container->get(GenerateSignedUriService::class);
        $user = UserFactory::find(UserFixtures::UNVERIFIED_USER_ULID);

        $this->client->request('GET', $generateSignedUriService->generateSignedUri($user));

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    #[Test]
    public function asAnonymousICantVerifyWithExpiredLink(): void
    {
        $container = static::getContainer();
        $clock = new MockClock(new \DateTimeImmutable('-48 hours'));

        $generateSignedUriService = new GenerateSignedUriService(
            $container->get(UriSigner::class),
            $container->get(UrlGeneratorInterface::class),
            $clock
        );
        $user = UserFactory::find(UserFixtures::UNVERIFIED_USER_ULID);

        $signedUri = $generateSignedUriService->generateSignedUri($user);

        $this->client->request('GET', $signedUri);
        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    #[Test]
    public function asAnonymousICantVerifyWithSameLinkTwice(): void
    {
        $container = static::getContainer();
        /** @var GenerateSignedUriService $generateSignedUriService */
        $generateSignedUriService = $container->get(GenerateSignedUriService::class);
        $user = UserFactory::find(UserFixtures::UNVERIFIED_USER_ULID);

        $signedUri = $generateSignedUriService->generateSignedUri($user);

        $this->client->request('GET', $signedUri);
        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->client->request('GET', $signedUri);
        self::assertResponseStatusCodeSame(Response::HTTP_GONE);
    }

    #[Test]
    public function asAnonymousICantVerifyAnAccountAlreadyValidated(): void
    {
        $container = static::getContainer();
        $generateSignedUriService = $container->get(GenerateSignedUriService::class);
        $user = UserFactory::find(UserFixtures::ADMIN_ULID);

        $this->client->request('GET', $generateSignedUriService->generateSignedUri($user));

        self::assertResponseStatusCodeSame(Response::HTTP_GONE);
    }

    #[Test]
    public function asAnonymousICantVerifyAnAccountWithInvalidHash(): void
    {
        $this->client->request('GET', sprintf('/users/%s/verify/email?_hash=invalid', UserFixtures::UNVERIFIED_USER_ULID));

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    #[Test]
    public function asAnonymousICannotRegisterWithInvalidMimeType(): void
    {
        $this->client->request('POST', '/users', [
            'headers' => [
                'Content-Type' => 'text/plain',
            ],
            'body' => '{"email":"foo@example.com"}',
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
    }

    public static function invalidData(): \Generator
    {
        yield 'empty_values' => [
            'email' => null,
            'password' => null,
            'countryCode' => null,
            'username' => null,
            'violations' => [
                [
                    'propertyPath' => 'email',
                    'message' => 'This value should not be blank.',
                ],
                [
                    'propertyPath' => 'username',
                    'message' => 'This value should not be blank.',
                ],
                [
                    'propertyPath' => 'password',
                    'message' => 'This value should not be blank.',
                ],
            ],
        ];

        yield 'omitted_email' => [
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

        yield 'omitted_password' => [
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

        yield 'invalid_email' => [
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

        yield 'invalid_country_code' => [
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

        yield 'invalid_password' => [
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

        yield 'email_already_exists' => [
            'email' => 'admin@esports-videos.com',
            'password' => UserFixtures::DEFAULT_PASSWORD,
            'countryCode' => 'FR',
            'username' => 'correct_username',
            'violations' => [
                [
                    'propertyPath' => 'email',
                    'message' => 'This value is already used.',
                ],
            ],
        ];
    }

    /**
     * @param array<string, mixed> $data
     */
    private function register(array $data): void
    {
        $this->client->request('POST', '/users', [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode($data, JSON_THROW_ON_ERROR),
        ]);
    }
}
