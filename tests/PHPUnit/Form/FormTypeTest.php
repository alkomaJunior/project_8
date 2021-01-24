<?php
/**
 * ToDoAndCo Project
 * Copyright (c) 2020 BigBoss 2020.  BigBoss Oualid
 * mailto: <bigboss@it-bigboss.de>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 * Inc., Munich, Germany.
 */

namespace App\Tests\PHPUnit\Form;

use App\Entity\Task;
use App\Entity\User;
use App\Form\DataTransferObject\PasswordUpdate;
use App\Form\TaskType;
use App\Form\User\AccountType;
use App\Form\User\UpdatePasswordType;
use App\Form\User\UserType;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class FormTypeTest extends TypeTestCase
{
    public function testCreateTaskValidData(): void
    {
        $data = [
            'title' => 'Title Test',
            'content' => 'Content test',
        ];
        $formModel = new Task();
        $expectedObject = (new Task())
            ->setTitle($data['title'])
            ->setContent($data['content']);

        $this->formHasError($formModel, $expectedObject, $data, TaskType::class);
    }

    public function testCreateUserValidData(): void
    {
        $data = [
            'username' => 'username',
            'email' => 'mail@todo.de',
            'password' => ['first' => 'Password*1', 'second' => 'Password*1'],
            'roles' => User::ROLE_USER,
        ];
        $formModel = new User();
        $expectedObject = (new User())
            ->setUsername($data['username'])
            ->setEmail($data['email'])
            ->setPassword($data['password']['first']);

        $this->formHasError($formModel, $expectedObject, $data, UserType::class);
    }

    public function testUpdateHisOwnAccount(): void
    {
        $data = [
            'username' => 'username',
            'email' => 'mail@todo.de',
        ];
        $formModel = new User();
        $expectedObject = (new User())
            ->setUsername($data['username'])
            ->setEmail($data['email']);

        $option = ['update_account' => true];

        $this->formHasError($formModel, $expectedObject, $data, AccountType::class, $option);
    }

    public function testUpdateAccountFromAdmin(): void
    {
        $data = [
            'username' => 'username',
            'email' => 'mail@todo.de',
            'roles' => User::ROLE_ADMIN,
        ];
        $formModel = new User();
        $expectedObject = (new User())
            ->setUsername($data['username'])
            ->setEmail($data['email'])
            ->setRoles([$data['roles']]);

        $option = ['update_account' => false];

        $this->formHasError($formModel, $expectedObject, $data, AccountType::class, $option);
    }

    public function testUpdateHisOwnPassword(): void
    {
        $data = [
            'actualPassword' => 'test1',
            'newPassword' => 'Password*1',
            'confirmPassword' => 'Password*1',
        ];
        $formModel = new PasswordUpdate();
        $expectedObject = (new PasswordUpdate())
            ->setActualPassword($data['actualPassword'])
            ->setNewPassword($data['newPassword'])
            ->setConfirmPassword($data['confirmPassword']);
        $options =[
            'update_account' => true,
            'validation_groups' => ['account']
        ];


        $this->formHasError($formModel, $expectedObject, $data, UpdatePasswordType::class, $options);
    }

    public function testUpdatePasswordFromAdmin(): void
    {
        $data = [
            'newPassword' => 'Password*1',
            'confirmPassword' => 'Password*1',
        ];
        $formModel = new PasswordUpdate();
        $expectedObject = (new PasswordUpdate())
            ->setNewPassword($data['newPassword'])
            ->setConfirmPassword($data['confirmPassword']);
        $options =[
            'update_account' => false,
            'validation_groups' => ['']
        ];


        $this->formHasError($formModel, $expectedObject, $data, UpdatePasswordType::class, $options);
    }

    /**
     * Register FormTypeValidatorExtension in the FormFactory
     * for more Info: https://github.com/symfony/symfony/issues/22593.
     */
    protected function getExtensions(): array
    {
        $validator = Validation::createValidator();

        // or if you also need to read constraints from annotations
        /*$validator = Validation::createValidatorBuilder()
            ->enableAnnotationMapping()
            ->getValidator();*/

        return [
            new ValidatorExtension($validator),
        ];
    }

    protected function formHasError(
        object $formModel,
        object $expectedObject,
        array $data,
        string $entityClass,
        array $options = []
    ): void {
        // $data will retrieve data from the form submission; pass it as the second argument
        $form = $this->factory->create($entityClass, $formModel, $options);
        $this->checkSubmitValidData($formModel, $expectedObject, $data, $form);

        $this->checkFormView($data, $form);
    }

    protected function checkSubmitValidData(
        object $formModel,
        object $expectedObject,
        array $data,
        FormInterface $form
    ): void {
        // Submit the data to the form directly
        $form->submit($data);
        // This check ensures there are no transformation failures
        $this->assertTrue($form->isSynchronized());

        // check that $data was modified as expected when the form was submitted
        $this->assertEquals($expectedObject, $formModel);
    }

    protected function checkFormView(array $data, FormInterface $form): void
    {
        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($data) as $key) {
            $this->assertArrayHasKey($key, $children);

            if (in_array($key, $children)) {
                $this->assertTrue(in_array($view->vars[$key], $data));
            }
        }
    }
}
