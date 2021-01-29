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
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Build form to update users infos: username & email.
 */
class AccountType extends AbstractUserForm
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, ['empty_data' => ''])
            ->add('email', EmailType::class, ['empty_data' => ''])
        ;
        // Display select role field only for admin.
        if (!$options['update_account']) {
            $builder->add(
                'roles',
                ChoiceType::class,
                [
                    'choices' => parent::getRolesOptions(),
                ]
            );
            // Select only one role for user (Hierarchy strategy )
            parent::transformRolesType($builder);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'translation_domain' => 'forms',
            'update_account' => Boolean::class,
        ]);
    }
}
