<?php

namespace App\Form;

use ReflectionClass;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class UserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, ['label' => "Nom d'utilisateur"])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les deux mots de passe doivent correspondre.',
                'required' => true,
                'first_options' => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Tapez le mot de passe à nouveau'],
            ])
           ->add('roles', ChoiceType::class, [
                'multiple' => true,
                'choices' => $this->getRolesOptions(),
            ])
            ->add('email', EmailType::class, ['label' => 'Adresse email'])
        ;
    }

    /**
     * Retrieve user roles as array and add them to choice type in form.
     */
    private function getRolesOptions(): array
    {
        // Retrieve All constants from class user
        $reflector = new ReflectionClass('App\Entity\User');
        $constants = $reflector->getConstants();
        $values = [];

        // Add roles with new key in array
        foreach ($constants as $constant => $value) {
            $prefix = 'ROLE_';

            if (false !== strpos($constant, $prefix)) {
                $key = strtolower(str_replace($prefix, '', $value));
                $values[ucfirst($key)] = $value;
            }
        }

        return $values;
    }
}
