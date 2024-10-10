<?php

namespace App\Controller;

use App\Entity\Character;
use App\Entity\Equipment;
use App\Entity\Faction;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Attributes\Get;
use OpenApi\Attributes\Info;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Post;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\RequestBody;
use OpenApi\Attributes\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Routing\Annotation\Route;

#[Info(version: "0.1", title: "API Backend Coding Task")]
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

    #[Route('/', name: 'create', methods: ['POST'])]
    #[Post(
        path: '/',
        operationId: 'createCharacter',
        description: 'Create a new character',
        summary: 'Create a new character',
        requestBody: new RequestBody(
            required: true,
            content: new JsonContent(
                required: ["name", "birth_date", "equipment_id", "faction_id", "kingdom"],
                properties: [
                    new Property(property: "name", type: "string", example: "John Snow"),
                    new Property(property: "birth_date", type: "string", example: "1201-09-07"),
                    new Property(property: "kingdom", type: "string", example: "Westeros"),
                    new Property(property: "equipment_id", type: "int", example: "1"),
                    new Property(property: "faction_id", type: "int", example: "2"),
                ],
            )
        ),
        responses: [
            new Response(response: 200, description: 'Character created'),
            new Response(response: 401, description: 'Not allowed')
        ],
    )
    ]
    public function create(Request $request): JsonResponse
    {
        $characterData = json_decode($request->getContent(), true);
        $newCharacter = new Character(
            name: $characterData['name'],
            birth_date: new DateTimeImmutable($characterData['birth_date']),
            kingdom: $characterData['kingdom'],
            equipment: $this->entityManager->getRepository(Equipment::class)->find($characterData['equipment_id']),
            faction: $this->entityManager->getRepository(Faction::class)->find($characterData['faction_id']),
        );

        $this->entityManager->persist($newCharacter);
        $this->entityManager->flush();

        return $this->json([
            'id' => $newCharacter->getId()
        ]);
    }
}
