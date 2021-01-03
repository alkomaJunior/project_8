<?php

/**
 * ToDoAndCo Project
 * Copyright (c) 2020 BigBoss 2021.  BigBoss Oualid
 * mailto: <bigboss@it-bigboss.de>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 * Inc., Munich, Germany.
 */

namespace App\Helper;

use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Manage Url.
 */
trait UrlManagerTrait
{
    /**
     * Check if referer returned from form.
     *
     * @param string | null $refererUrl
     * @param string        $defaultUrl
     *
     * @return string
     */
    private function validReferer(?string $refererUrl, string $defaultUrl): string
    {
        //if refererUrl equals null return user to default url
        return $refererUrl ? $refererUrl : $defaultUrl;
    }

    /**
     * Check if url returned from form.
     *
     * @param UserInterface | null $user
     * @param string               $defaultUrl
     *
     * @return string
     */
    private function urlForRole(?UserInterface $user, string $defaultUrl): string
    {
        // If refererUrl equals null return user to default url
        if ($user && in_array(User::ROLE_ADMIN, $user->getRoles())) {
            return $defaultUrl;
        }
        // If refererUrl equals null return user to default url
        return 'homepage';
    }
}
