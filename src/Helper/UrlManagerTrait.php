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

/**
 * Manage Url.
 */
trait UrlManagerTrait
{
    /**
     * Check if referer returned from form.
     *
     * @param string | null $refererRoute
     * @param string        $defaultRoute
     *
     * @return string
     */
    private function validReferer(?string $refererRoute, string $defaultRoute): string
    {
        //If refererUrl equals null return user to default url
        return $refererRoute ? $refererRoute : $defaultRoute;
    }

    /**
     * Return the given url if user is admin.
     *
     * @param string[] $roles
     * @param string   $route
     *
     * @return string
     */
    private function getRoute(array $roles, string $route): string
    {
        // If user is not admin return homepage
        return in_array(User::ROLE_ADMIN, $roles) ? $route : 'homepage';
    }
}
