<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

class DocsController extends AbstractController
{
    #[Route('/docs', name: 'docs_index', methods: ['GET'], format: 'yaml')]
    #[Cache(maxage: 3600, public: true, mustRevalidate: true)]
    public function index(KernelInterface $kernel): Response
    {
        return new Response(
            file_get_contents($kernel->getProjectDir().'/var/openapi.yaml'),
            headers: ['Content-Type' => 'yaml'],
        );
    }
}