<?php
/**
 * ToDoAndCo Project
 * Copyright (c) 2020 BigBoss 2020.  BigBoss Oualid
 * mailto: <bigboss@it-bigboss.de>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 * Inc., Munich, Germany.
 */

namespace App\Tests\PHPUnit\Helper;

use Symfony\Component\Validator\ConstraintViolation;

trait HelperTrait
{
    protected function assertHasErrors(object $entity, int $errorNumber = 0, bool $hasBootKernel = true): void
    {
        if($hasBootKernel){
            self::bootKernel();
        }

        $errors = self::$container->get('validator')->validate($entity);
        $messages = [];

        $numErrors = count($errors);
        $i = 0;

        /** @var ConstraintViolation $error */
        foreach ($errors as $error) {
            $messages[] = $error->getPropertyPath().' => '.$error->getMessage();
            if (++$i === $numErrors) {
                $messages[] = "\n";
            }
        }
        $this->assertCount($errorNumber, $errors, implode("\n", $messages));
    }
}
