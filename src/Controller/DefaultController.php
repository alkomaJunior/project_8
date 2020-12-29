<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request): Response
    {
        $response = $this->render('default/index.html.twig');

        $response->setEtag(md5($response->getContent()));
        $response->setPublic();
        if ($response->isNotModified($request)) {
            return $response;
        }

        return $response;
    }
}
