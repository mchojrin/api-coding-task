<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home', format: "json")]
    #[Cache(maxage: 3600, public: true, mustRevalidate: true)]
    public function home(): JsonResponse
    {
        return new JsonResponse([
            'characters' => $this->generateUrl('characters_index'),
            'factions' => $this->generateUrl('factions_index'),
            'equipments' => $this->generateUrl('equipments_index'),
            'docs' => $this->generateUrl('docs_index'),
        ]);
    }
}