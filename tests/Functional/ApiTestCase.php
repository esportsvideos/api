<?php

namespace App\Tests\Functional;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase as BaseApiTest;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

abstract class ApiTestCase extends BaseApiTest
{
    protected Client $client;

    protected function setUp(): void
    {
        static::$alwaysBootKernel = false;
        $this->client = self::createClient([], ['headers' => ['Accept' => 'application/ld+json']]);
    }

    public function authenticateTestClientAs(string $id): Client
    {
        $user = static::getContainer()->get('doctrine')->getRepository(User::class)->findOneById($id);

        if (!$user instanceof User) {
            throw new \RuntimeException('User not found');
        }

        $tokenManager = static::getContainer()->get(JWTTokenManagerInterface::class);
        $token = $tokenManager->create($user);

        $this->client->setDefaultOptions(['headers' => ['Authorization' => 'Bearer '.$token]]);

        return $this->client;
    }
}
