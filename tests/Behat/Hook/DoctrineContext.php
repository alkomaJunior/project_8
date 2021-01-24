<?php
/**
 * ToDoAndCo Project
 * Copyright (c) 2020 BigBoss 2020.  BigBoss Oualid
 * mailto: <bigboss@it-bigboss.de>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 * Inc., Munich, Germany.
 */

declare(strict_types=1);

namespace App\Tests\Behat\Hook;


use App\DataFixtures\AppFixtures;
use Behat\Behat\Context\Context;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\ToolsException;

class DoctrineContext implements Context
{
    /**
     * @var AppFixtures
     */
    private $fixtures;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(AppFixtures $fixtures, EntityManagerInterface $em)
    {
        $this->fixtures = $fixtures;
        $this->em = $em;
    }

    /**
     * @BeforeScenario @createSchema
     *
     * @throws ToolsException
     */
    public function createSchema(): void
    {
        //Get entity metadata
        $classes = $this->em->getMetadataFactory()->getAllMetadata();

        //Drop & create schema
        $schemaTool = new SchemaTool($this->em);
        $schemaTool->dropSchema($classes);
        $schemaTool->createSchema($classes);

        //load fixtures... & execute
        $purger = new ORMPurger($this->em);
        $fixturesExecutor =
            new ORMExecutor(
                $this->em,
                $purger
            );
        $fixturesExecutor->execute([
            $this->fixtures,
        ]);
    }
}