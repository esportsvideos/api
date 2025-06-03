<?php

namespace DataFixtures\Data;

use DataFixtures\FixturesSizeEnum;
use Doctrine\Bundle\FixturesBundle\Fixture as DoctrineFixture;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Id\AssignedGenerator;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bridge\Doctrine\IdGenerator\UlidGenerator;

abstract class AbstractFixture extends DoctrineFixture
{
    public function __construct(
        private readonly FixturesSizeEnum $fixturesSize,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        /** @var EntityManager $em */
        $em = $manager;
        $em->getConnection()->getConfiguration()->setMiddlewares([]);

        /** @var ClassMetadata<object> $metadata */
        $metadata = $em->getClassMetadata($this->getFixtureEntityClass());

        $metadata->setIdGenerator(new AssignedGenerator());
        $this->loadSpecificFixtures();

        $metadata->setIdGenerator(new UlidGenerator());
        for ($i = 1; $i <= $this->fixturesSize->getFixtureSize(); ++$i) {
            $this->createRandomEntity();
            $manager->clear();
        }
    }

    abstract public function createRandomEntity(): void;

    abstract public function loadSpecificFixtures(): void;

    /** @return class-string */
    abstract public function getFixtureEntityClass(): string;
}
