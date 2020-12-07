<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Yaml\Yaml;

/**
 * Class AppFixtures.
 */
final class AppFixtures extends Fixture
{
    /**
     * Retrieve fixtures from file and transform them to array.
     *
     * @param string $entityName
     * @SuppressWarnings(PHPMD.StaticAccess)
     *
     * @return array
     */
    private function getDataFixture(string $entityName): array
    {
        return Yaml::parse(file_get_contents(__DIR__.'/Fixtures/'.$entityName.'s.yaml', true));
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $users = $this->getDataFixture('User');
        $tasks = $this->getDataFixture('Task');

        $this->addUsers($users, $manager);
        $this->addTasks($tasks, $manager);

        $manager->flush();
        echo "\n Loading fixtures is terminated!\n";
    }

    /**
     * Create users.
     *
     * @param array         $users
     * @param ObjectManager $manager
     */
    private function addUsers(array $users, ObjectManager $manager): void
    {
        // Create users
        foreach ($users as $name => $user) {
            /** @var User $userEntity */
            $userEntity = new User();

            $userEntity->setUsername($user['Username'])
                ->setPassword($user['Password'])
                ->setEmail($user['Email'])
                ->setRoles($user['Roles']);

            $manager->persist($userEntity);
            $this->addReference($name, $userEntity);
        }
    }

    /**
     * Create tasks.
     *
     * @param array         $tasks
     * @param ObjectManager $manager
     */
    private function addTasks(array $tasks, ObjectManager $manager): void
    {
        foreach ($tasks as $task) {
            /** @var Task $userEntity */
            $taskEntity = new Task();

            $taskEntity->setTitle($task['Title'])
                ->setContent($task['Content'])
                ->setCreatedAt(new DateTime());

            if (isset($task['Reference']['Author'])) {
                /** @var User $author */
                $author = $this->getReference($task['Reference']['Author']);
                $taskEntity->setUser($author);
            }

            $manager->persist($taskEntity);
        }
    }
}
