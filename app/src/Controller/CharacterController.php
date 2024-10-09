<?php

namespace App\Controller;

use App\Entity\Character;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Attributes\Get;
use OpenApi\Attributes\Info;
use OpenApi\Attributes\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Routing\Annotation\Route;

#[Info(title: "API Backend Coding Task", version: "0.1")]
#[Route('/characters', name: 'characters_', methods: ['GET'])]
class CharacterController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    #[Route('/', name: 'index', methods: ['GET'])]
    #[Cache(maxage: 3600, public: true, mustRevalidate: true)]
    #[Get(
        path: '/characters',
        operationId: 'getCharacters',
        description: 'Get all characters.',
        summary: 'Get all characters in the database.',
        responses: [
            new Response(response: 200, description: 'OK'),
            new Response(response: 401, description: 'Not allowed')
        ]
    )]
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
    #[Get(
        path: '/characters/{id}',
        operationId: 'getCharacter',
        description: 'Get a specific character.',
        summary: 'Get a specific character details.',
        responses: [
            new Response(response: 200, description: 'OK'),
            new Response(response: 401, description: 'Not allowed')
        ]
    )]
    public function detail(Character $character): JsonResponse
    {
        return $this->json($character);
    }
}
