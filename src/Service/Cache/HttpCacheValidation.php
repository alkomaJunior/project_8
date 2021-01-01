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

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

/**
 * Manage http CacheValidation.
 */
class HttpCacheValidation implements CacheValidationInterface
{
    private RequestStack $requestStack;

    /**
     * HttpCacheValidation constructor.
     *
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function set(Response $response): Response
    {
        $response->setEtag(md5($response->getContent()));
        $response->setPublic();

        if ($response->isNotModified($this->requestStack->getCurrentRequest())) {
            return $response;
        }

        return $response;
    }
}
