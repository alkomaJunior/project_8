<?php

/**
 * ToDoAndCo Project
 * Copyright (c) 2020 BigBoss 2020.  BigBoss Oualid
 * mailto: <bigboss@it-bigboss.de>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 * Inc., Munich, Germany.
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Manage user Log in & logout.
 */
class SecurityController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     *
     * @param AuthenticationUtils $authenticationUtils
     *
     * @return Response
     */
    public function homepageAction(AuthenticationUtils $authenticationUtils): Response
    {
        return $this->render('homepage/index.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }

    /**
     * @Route("/logout", name="logout")
     *
     * @codeCoverageIgnore
     *
     * @return void
     */
    public function logoutCheck(): void
    {
    }
}
