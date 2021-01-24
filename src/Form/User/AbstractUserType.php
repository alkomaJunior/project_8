<?php
/**
 * ToDoAndCo Project
 * Copyright (c) 2020 BigBoss 2020.  BigBoss Oualid
 * mailto: <bigboss@it-bigboss.de>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 * Inc., Munich, Germany.
 */

namespace App\Form\User;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractUserType extends AbstractType
{
    /**
     * Retrieve user roles from entity User as array and add them to choice type in form.
     */
    protected function getRolesOptions(): array
    {
        $roles = User::ALL_ROLES;
        $values = [];

        // Add roles with new key in array
        // Change translation in the file translations/forms.fr.yaml
        foreach ($roles as $role) {
            $prefix = 'ROLE_';
            $key = strtolower(str_replace($prefix, '', $role));
            $values[ucfirst($key)] = $role;
        }

        return $values;
    }

    /**
     * Transform roles type from string to array or array to string.
     *
     * @param FormBuilderInterface $builder
     */
    protected function transformRolesType(FormBuilderInterface $builder): void
    {
        $builder->get('roles')
            ->addModelTransformer(new CallbackTransformer(
                function (array $rolesArray) {
                    // transform the array to a string
                    return count($rolesArray) ? $rolesArray[0] : null;
                },
                function (string $rolesString) {
                    // transform the string back to an array
                    return [$rolesString];
                }
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'translation_domain' => 'forms',
            'logged_user' => User::class,
        ]);
    }
}
