<?php
/**
 * ToDoAndCo Project
 * Copyright (c) 2020 BigBoss 2020.  BigBoss Oualid
 * mailto: <bigboss@it-bigboss.de>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 * Inc., Munich, Germany.
 */

namespace App\Tests\Helper;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Response;

trait FormTrait
{
    protected function submitForm(
        KernelBrowser $client,
        string $formSelector,
        array $data = [],
        string $uri = '',
        array $server = []
    ): void {
        $crawler = $client->request('GET', $uri, [], [], $server);

        $this->assertResponseIsSuccessful();

        $form = $crawler->filter($formSelector);
        $form = $form->form(
            $data
        );

        $client->submit($form);
    }

    protected function formHasError(KernelBrowser $client, string $expectedLocation): void
    {
        $this->assertResponseRedirects(
            $expectedLocation,
            Response::HTTP_FOUND
        );
        $client->followRedirect();
    }
}
