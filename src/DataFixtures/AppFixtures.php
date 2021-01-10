<?php

/**
 * ToDoAndCo Project
 * Copyright (c) 2020 BigBoss 2020.  BigBoss Oualid
 * mailto: <bigboss@it-bigboss.de>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 * Inc., Munich, Germany.
 */

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Yaml\Parser;

/**
 * Class AppFixtures.
 */
class AppFixtures extends Fixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $users = $this->getDataFixture('User');
        $tasks = $this->getDataFixture('Task');
        // Add 2 users
        $usersNum = $this->persistFixtures($manager, $users, User::class);
        // Add 6 tasks
        $tasksNum = $this->persistFixtures($manager, $tasks, Task::class);

        $manager->flush();

        $successMessage = "\033[0;32m";
        $successMessage .= "   > ".$usersNum." users & ".$tasksNum." tasks are loaded!\n";
        $successMessage .= "   > Loading fixtures is terminated!\n";
        $successMessage .= "\e[0m";
        print_r($successMessage);
    }

    /**
     * Retrieve fixtures from file and transform them to array.
     *
     * @param string $entityName
     *
     * @return array[]
     */
    private function getDataFixture(string $entityName): array
    {
        $yaml = new Parser();
        $fileName = __DIR__.'/Fixtures/'.$entityName.'s.yaml';

        if (file_exists($fileName)) {
            /** @var string $input */
            $input = file_get_contents($fileName, true);

            return $yaml->parse($input);
        }

        return [];
    }

    /**
     * @param ObjectManager       $manager
     * @param array<string,array> $fixtures
     * @param string              $className
     *
     * @return int
     */
    private function persistFixtures(ObjectManager $manager, array $fixtures, string $className): int
    {
        $count = 0;
        foreach ($fixtures as $name => $properties) {
            $entity = $this->createEntity($className, $properties);

            $manager->persist($entity);

            if (!$this->hasReference($name)) {
                $this->addReference($name, $entity);
            }
            ++$count;
        }

        return $count;
    }

    /**
     * @param string               $className
     * @param array<string, mixed> $properties
     *
     * @throws LogicException
     *
     * @return object
     */
    private function createEntity(string $className, array $properties): Object
    {
        if (!class_exists($className)) {
            throw new LogicException("Class '${className}' not found");
        }

        $entity = new $className();
        foreach ($properties as $property => $value) {
            if ('Reference' === $property) {
                foreach ($value as $referencedProperty => $referencedValue) {
                    $this->hydrateEntity(
                        $className,
                        $referencedProperty,
                        $entity,
                        $this->getReference($referencedValue)
                    );
                }
                break;
            }
            $this->hydrateEntity($className, $property, $entity, $value);
        }

        return $entity;
    }

    /**
     * @param mixed  $className
     * @param string $propertyName
     * @param object $entity
     * @param mixed  $attribute
     */
    private function hydrateEntity($className, string $propertyName, object $entity, $attribute): void
    {
        // Get the name of the setter corresponding to the attribute.
        $method = 'set'.ucfirst($propertyName);
        // If the corresponding setter exists.
        if (method_exists($className, $method)) {
            if (is_string($attribute) && 'NOW()' === $attribute) {
                $attribute = new DateTime();
            }
            // Call the setter.
            $entity->$method($attribute);
        }
    }
}
