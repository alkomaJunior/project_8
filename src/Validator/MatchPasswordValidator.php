<?php

/**
 * ToDoAndCo Project
 * Copyright (c) 2020 BigBoss 2020.  BigBoss Oualid
 * mailto: <bigboss@it-bigboss.de>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 * Inc., Munich, Germany.
 */

namespace App\Validator;

use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\LogicException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

/**
 * Validate actual password.
 */
class MatchPasswordValidator extends ConstraintValidator
{
    private Security $security;

    /**
     * MatchPasswordValidator constructor.
     */
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @inxheritDoc
     */
    public function validate($value, Constraint $constraint): void
    {
        if (null === $value || '' === $value) {
            return;
        }
        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
            // separate multiple types using pipes: e.g ($value, 'string|int');
        }

        $loggedUser = $this->security->getUser();

        if (null === $loggedUser) {
            throw new LogicException('User is not log in !');
        }

        // Verify if passwords don't match, then add violation
        if (!password_verify($value, $loggedUser->getPassword())) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', $value)
                ->addViolation();
        }
    }
}
