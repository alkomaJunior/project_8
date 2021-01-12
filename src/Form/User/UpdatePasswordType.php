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

use App\Form\DataTransferObject\PasswordInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Build form to update users password.
 */
class UpdatePasswordType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        //dd($options['validation_groups']);
        // Display input actualPassword field only by editing profile
        if (in_array('account', $options['validation_groups'])) {
            $builder->add('actualPassword', PasswordType::class, ['empty_data' => '']);
        }

        $builder->add('newPassword', PasswordType::class, ['empty_data' => ''])
            ->add('confirmPassword', PasswordType::class, ['empty_data' => ''])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PasswordInterface::class,
            'translation_domain' => 'forms',
            'validation_groups' => ['Default', 'account'],
        ]);
    }
}
