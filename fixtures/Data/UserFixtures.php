<?php

namespace DataFixtures\Data;

use App\Entity\User;
use DataFixtures\Factory\UserFactory;
use DataFixtures\FixturesSizeEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Id\AssignedGenerator;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bridge\Doctrine\IdGenerator\UlidGenerator;
use Symfony\Component\Uid\Ulid;

class UserFixtures extends Fixture
{
    public const string ADMIN_ULID = '019712ef-fb77-347f-1cd9-2b1c22d259e6';
    public const string USER_ULID = '019712ef-fb78-602c-a546-f7c694bd83be';

    public function __construct(private readonly FixturesSizeEnum $fixturesSize)
    {
    }

    public function load(ObjectManager $manager): void
    {
        /** @var EntityManager $em */
        $em = $manager;

        $em->getConnection()->getConfiguration()->setMiddlewares([]);

        /** @var ClassMetadata<User> $metadata */
        $metadata = $em->getClassMetadata(User::class);
        $metadata->setIdGenerator(new AssignedGenerator());

        UserFactory::new(['id' => Ulid::fromString(self::ADMIN_ULID), 'username' => 'admin'])->isAdmin()->create();
        UserFactory::new(['id' => Ulid::fromString(self::USER_ULID), 'username' => 'user'])->isAdmin()->create();

        $metadata->setIdGenerator(new UlidGenerator());

        for ($i = 1; $i <= $this->fixturesSize->getFixtureSize(); ++$i) {
            UserFactory::createOne();
            $manager->clear();
        }
    }
}
