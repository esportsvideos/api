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

use Behat\Behat\Context\Context;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\ToolsException;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @author Maxime Cornet <xelysion@icloud.com>
 */
class FeatureContext implements Context
{
    /**
     * @var ObjectManager
     */
    private $manager;

    /**
     * @var SchemaTool
     */
    private $schemaTool;

    /**
     * @var array
     */
    private $classes;

    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @param ManagerRegistry $doctrine
     * @param KernelInterface $kernel
     */
    public function __construct(ManagerRegistry $doctrine, KernelInterface $kernel)
    {
        $this->manager = $doctrine->getManager();
        $this->schemaTool = new SchemaTool($this->manager);
        $this->classes = $this->manager->getMetadataFactory()->getAllMetadata();
        $this->kernel = $kernel;
    }

    /**
     * @BeforeScenario @resetSchema
     *
     * @throws ToolsException
     * @throws Exception
     */
    public function resetSchema(): void
    {
        $this->schemaTool->dropSchema($this->classes);
        $this->schemaTool->createSchema($this->classes);

        $application = new Application($this->kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput(
            [
                'command' => 'doctrine:fixtures:load',
                '--no-interaction' => null,
            ]
        );

        $application->run($input, new NullOutput());
    }
}
