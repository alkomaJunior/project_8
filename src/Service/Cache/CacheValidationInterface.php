<?php

/**
 * ToDoAndCo Project
 * Copyright (c) 2020 BigBoss 2021.  BigBoss Oualid
 * mailto: <bigboss@it-bigboss.de>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 * Inc., Munich, Germany.
 */

namespace App\Service\Cache;

use Symfony\Component\HttpFoundation\Response;

/**
 * Interface CacheValidationInterface.
 */
interface CacheValidationInterface
{
    /**
     * Return response from cache if exists, if not Set it.
     *
     * @param Response $response
     *
     * @return Response
     */
    public function set(Response $response): Response;
}
