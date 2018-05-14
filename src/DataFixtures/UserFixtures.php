<?php

/*
 * This file is part of the Esports Videos project.
 *
 * (c) Esports Videos <https://github.com/esportsvideos/api/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Nelmio\Alice\Loader\NativeLoader;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Create the following list of users :
 *   - 1 admin account
 *   - 1 active user
 *   - 1 inactive user
 *   - 1 active user that have tokenExpired.
 *
 * Password of these users is esv.
 *
 * @author Maxime Cornet <xelysion@icloud.com>
 */
class UserFixtures extends Fixture
{
    /** @var UserPasswordEncoderInterface $encoder */
    private $encoder;

    /**
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $loader = new NativeLoader();
        $output = new ConsoleOutput();
        $users = $loader->loadFile(__DIR__.'/fixtures/users.yaml');
        $output->writeln(sprintf('    <comment>></comment> <info>%s</info>', 'Loading Users'));

        /** @var User $user */
        foreach ($users->getObjects() as $user) {
            $user->setPassword($this->encoder->encodePassword($user, $user->getPlainPassword()));
            $manager->persist($user);
        }

        $manager->flush();
    }
}
