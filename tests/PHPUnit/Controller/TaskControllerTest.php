<?php
/**
 * ToDoAndCo Project
 * Copyright (c) 2020 BigBoss 2020.  BigBoss Oualid
 * mailto: <bigboss@it-bigboss.de>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 * Inc., Munich, Germany.
 */

namespace App\Tests\PHPUnit\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Tests\PHPUnit\Helper\FormTrait;
use App\Tests\PHPUnit\Helper\LoginTrait;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    use LoginTrait;
    use FixturesTrait;
    use FormTrait;

    protected ?kernelBrowser $client = null;

    /**
     * @var User[]
     */
    protected array $dbData = [];

    protected function setUp(): void
    {
        self::ensureKernelShutdown();
        $this->client = static::createClient();
        $this->dbData = $this->loadFixtureFiles(
            [
                dirname(__DIR__).'/fixtures/tasks_users.yaml',
            ]
        );
    }

    public function testCreateInvalidTask(): void
    {
        $uri = '/tasks/create';
        $data = [
            'task[title]' => '',
            'task[content]' => 'Test Content',
            ];

        $this->logIn($this->dbData['user_1'], $this->client);
        $this->submitForm(
            $this->client,
            "form[action='".$uri."']",
            $data,
            $uri
        );

        $this->assertSelectorTextContains(
            '.form-error-message',
            'Vous devez saisir un titre.'
        );
    }

    public function testCreateTask(): void
    {
        $uri = '/tasks/create';
        $data = [
            'task[title]' => 'Task Title',
            'task[content]' => 'Task Content test',
            ];

        $this->logIn($this->dbData['user_1'], $this->client);
        $this->submitForm(
            $this->client,
            "form[action='".$uri."']",
            $data,
            $uri
        );

        $this->formHasError($this->client, '/tasks/done/false');
        $this->assertSelectorTextContains('.alert-success > span', 'La tâche a été bien été ajoutée.');
        $this->assertSelectorTextContains("a[href='/tasks/11/edit'] > h4", 'Task Title');
    }

    public function testEditTask(): void
    {
        $uri = '/tasks/10/edit';
        $httpReferer = '/tasks';
        $data = ['task[title]' => 'Title Changed'];
        $formSelector = "form[action='".$uri."']";

        $this->logIn($this->dbData['user_1'], $this->client);
        $this->submitForm(
            $this->client,
            $formSelector,
            $data,
            $uri,
            ['HTTP_referer' => $httpReferer]
        );

        $this->formHasError($this->client, $httpReferer);
        $this->assertSelectorTextContains('.alert-success > span', 'La tâche a bien été modifiée.');
        $this->assertSelectorTextContains("a[href='".$uri."'] > h4", 'Title Changed');
    }

    public function testToggleTask(): void
    {
        /** @var Task $task */
        $task = $this->dbData['task_not_done_1'];
        $uri = '/tasks/done/'.$task->urlIsDoneValue();
        $formSelector = "form[action='/tasks/1/toggle']";

        $this->logIn($this->dbData['user_1'], $this->client);
        $this->submitForm(
            $this->client,
            $formSelector,
            [],
            $uri,
            ['HTTP_referer' => $uri]
        );

        $this->formHasError($this->client, $this->client->getRequest()->getSchemeAndHttpHost().$uri);
        $this->assertSelectorTextContains(
            '.alert-success > span',
            'La tâche '.$task->getTitle().' a bien été marquée comme faite.'
        );

        $this->assertSelectorNotExists($formSelector, 'The Task should not be exist');
    }

    public function testDeleteTaskFromAuthor(): void
    {
        $uri = '/tasks';
        $formSelector = "form[action='/tasks/1/delete']";

        $this->logIn($this->dbData['user_1'], $this->client);
        $this->submitForm(
            $this->client,
            $formSelector,
            [],
            $uri,
            ['HTTP_referer' => $uri]
        );

        $this->formHasError($this->client, $this->client->getRequest()->getSchemeAndHttpHost().$uri);
        $this->assertSelectorTextContains(
            '.alert-success > span',
            'La tâche a bien été supprimée.'
        );
        $this->assertSelectorNotExists($formSelector, 'The Task should not be exist');
    }

    public function testDeleteTaskWithInvalidToken(): void
    {
        $uri = '/tasks';
        $data = ['_token' => 'fake_token'];
        $formSelector = "form[action='/tasks/1/delete']";

        $this->logIn($this->dbData['user_1'], $this->client);
        $this->submitForm(
            $this->client,
            $formSelector,
            $data,
            '/tasks',
            ['HTTP_referer' => $uri]
        );

        $this->formHasError($this->client, $this->client->getRequest()->getSchemeAndHttpHost().$uri);
        $this->assertSelectorTextContains(
            '.alert-warning > span',
            "la tâche n'a pas été supprimée. le token n'est pas valid!"
        );
        $this->assertSelectorExists($formSelector, 'The Task should still exist');
    }

    protected function tearDown(): void
    {
        unset($this->client);
        unset($this->user);
    }
}
