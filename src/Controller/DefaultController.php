<?php

namespace App\Controller;

use App\Service\VersionFetcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="default")
     */
    public function index(VersionFetcher $fetcher)
    {
        return new JsonResponse($fetcher->get());
    }
}
