<?php

/**
 * ToDoAndCo Project
 * Copyright (c) 2020 BigBoss 2021.  BigBoss Oualid
 * mailto: <bigboss@it-bigboss.de>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 * Inc., Munich, Germany.
 */

use App\Service\Cache\HttpCacheValidation;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Request;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

class HttpCacheValidationTest extends TestCase
{
    /**
     * @var MockObject|RequestStack|null  $requestStack
     */
    private ?MockObject $requestStack;

    /**
     * @var MockObject|Request|null  $request
     */
    private ?MockObject $request;

    /**
     * @var HttpCacheValidation|null $response
     */
    private ?HttpCacheValidation $cacheValidation;

    protected function setUp(): void
    {
        $this->requestStack = $this->getMockBuilder(RequestStack::class)->disableOriginalConstructor()->getMock();
        $this->request = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->getMock();
        $this->cacheValidation = new HttpCacheValidation($this->requestStack);
    }

    public function testSetResponseInCache(): void
    {
        $this->requestStack->expects($this->once())->method('getCurrentRequest')->willReturn($this->request);

        $result = $this->cacheValidation->set(new Response('content'));

        $this->assertInstanceOf(
            Response::class,
            $result,
            'The Method should return Response instance'
        );
        $this->assertNotNull(
            $result->getEtag(),
            'The service should set "Etag" value'
        );
        $this->assertEquals(
            $result->headers->get('cache-control'),
            'public',
            'the "cache-control" value should be "public"'
        );
    }

    public function testGetResponseFromCache(): void
    {
        $response = $this->getMockBuilder(Response::class)->enableOriginalConstructor()->getMock();

        $this->requestStack->expects($this->once())->method('getCurrentRequest')->willReturn($this->request);
        $response->expects($this->once())->method('isNotModified')->willReturn(true);

        $this->assertInstanceOf(
            Response::class,
            $this->cacheValidation->set($response),
            'The Method should return Response instance'
        );
    }

    protected function tearDown(): void
    {
        $this->requestStack = null;
        $this->request = null;
        $this->cacheValidation = null;
    }
}
