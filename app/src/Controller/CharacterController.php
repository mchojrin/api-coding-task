<?php

namespace App\Controller;

use App\Entity\Character;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/characters', name: 'characters_', methods: ['GET'])]
class CharacterController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    #[Route('/', name: 'index', methods: ['GET'])]
    #[Cache(maxage: 3600, public: true, mustRevalidate: true)]
    public function index(): JsonResponse
    {
        return $this->json(
            $this
                ->entityManager
                ->getRepository(Character::class)
                ->findAll()
        );
    }

    #[Route('/{id}', name: 'detail', methods: ['GET'])]
    #[Cache(maxage: 3600, public: true, mustRevalidate: true)]
    public function detail(Character $character): JsonResponse
    {
        return $this->json($character);
    }
}
