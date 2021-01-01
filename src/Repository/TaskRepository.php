<?php
/**
 * ToDoAndCo Project
 * Copyright (c) 2020 BigBoss 2020.  BigBoss Oualid
 * mailto: <bigboss@it-bigboss.de>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 * Inc., Munich, Germany.
 */

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    /**
     * TaskRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    /**
     * Retrieve task list based on the value of isDone.
     *
     * @param string|null $isDone
     *
     * @return array
     */
    public function findTasks(?string $isDone = null): array
    {
        $queryBuilder = $this->createQueryBuilder('t')
            ->addSelect('u')
            ->leftJoin('t.user', 'u');

        if (null !== $isDone) {
            $queryBuilder = $this->filterTasks($queryBuilder, $isDone);
        }

        return $queryBuilder
            ->orderBy('t.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retrieve done or not done tasks.
     *
     * @param QueryBuilder $queryBuilder
     * @param string       $isDone
     *
     * @return QueryBuilder
     */
    private function filterTasks(QueryBuilder $queryBuilder, string $isDone): QueryBuilder
    {
        // Convert string value to bool e.g: 'true'= True | false = False
        $isDone = filter_var($isDone, FILTER_VALIDATE_BOOLEAN);
        $queryBuilder
            ->Where('t.isDone = :val')
            ->setParameter('val', $isDone);

        return $queryBuilder;
    }
}
