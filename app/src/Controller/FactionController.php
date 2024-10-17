<?php

namespace App\Controller;

use App\Entity\Faction;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use OpenApi\Attributes\Delete;
use OpenApi\Attributes\Get;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Parameter;
use OpenApi\Attributes\Patch;
use OpenApi\Attributes\Post;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\RequestBody;
use OpenApi\Attributes\Response;
use OpenApi\Attributes\Schema;

#[Route('/factions', name: 'factions_', format: 'json')]
class FactionController extends AbstractController
{

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    #[Route('/', name: 'index', methods: ['GET'])]
    #[Cache(maxage: 3600, public: true, mustRevalidate: true)]
    #[Get(
        path: '/factions',
        operationId: 'listFactions',
        description: 'List of all factions in the database',
        summary: 'List of all factions',
        responses: [
            new Response(response: 200, description: 'OK'),
            new Response(response: 401, description: 'Not allowed')
        ]
    )]
    public function index(): JsonResponse
    {
        return $this->json($this
            ->entityManager
            ->getRepository(Faction::class)
            ->findAll()
        );
    }

    #[Route('/{id}', name: 'detail', methods: ['GET'])]
    #[Cache(maxage: 3600, public: true, mustRevalidate: true)]
    #[Get(
        path: '/factions/{id}',
        operationId: 'getFaction',
        description: 'Create a new faction',
        summary: 'Create a new faction',
        parameters: [
            new Parameter(name: "id", description: 'Faction id', in: 'path', required: true, example: "1", schema: new Schema(
                type:"integer"
            )),
        ],
        responses: [
            new Response(response: 200, description: 'OK'),
            new Response(response: 401, description: 'Not allowed')
        ]
    )]
    public function detail(Faction $faction): JsonResponse
    {
        return $this->json($faction);
    }

    #[Route('/', name: 'create', methods: ['POST'])]
    #[Post(
        path: '/factions',
        operationId: 'createFaction',
        description: 'Create a new faction',
        summary: 'Create a new faction',
        requestBody: new RequestBody(
            required: true,
            content: new JsonContent(
                required: ["faction_name", "description"],
                properties: [
                    new Property(property: "faction_name", type: "string", example: "Great Faction"),
                    new Property(property: "description", type: "string", example: "The greatest faction there is"),
                ],
            )
        ),
        responses: [
            new Response(response: 200, description: 'OK'),
            new Response(response: 401, description: 'Not allowed')
        ],
    )]
    #[IsGranted("IS_AUTHENTICATED")]
    public function create(Request $request): JsonResponse
    {
        $requestBody = json_decode($request->getContent(), true);

        $faction = new Faction(
            faction_name: $requestBody['faction_name'],
            description: $requestBody['description'],
        );

        $this->entityManager->persist($faction);
        $this->entityManager->flush();

        return $this->json(
            [
                'id' => $faction->getId(),
            ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    #[IsGranted("IS_AUTHENTICATED")]
    #[Delete(
        path: '/factions/{id}',
        operationId: 'deleteFaction',
        description: 'Delete a faction',
        summary: 'Delete a faction',
        parameters: [
            new Parameter(name: "id", description: 'Faction id', in: 'path', required: true, example: "1", schema: new Schema(
                type:"integer"
            )),
        ],
        responses: [
            new Response(response: 200, description: 'OK'),
            new Response(response: 401, description: 'Not allowed')
        ],
    )]
    public function delete(Faction $toDelete): JsonResponse
    {
        $this->entityManager->remove($toDelete);
        $this->entityManager->flush();

        return $this->json([]);
    }

    #[Route('/{id}', name: 'update', methods: ['PATCH'])]
    #[IsGranted("IS_AUTHENTICATED")]
    #[Patch(
        path: '/factions/{id}',
        operationId: 'updateFaction',
        description: 'Update an faction',
        summary: 'Update an faction',
        requestBody: new RequestBody(
            required: true,
            content: new JsonContent(
                properties: [
                    new Property(property: "faction_name", type: "string", example: "Great faction"),
                    new Property(property: "description", type: "string", example: "The greatest faction there is"),
                ],
            )
        ),
        parameters: [
            new Parameter(name: "id", description: 'Faction id', in: 'path', required: true, example: "1", schema: new Schema(
                type:"integer"
            )),
        ],
        responses: [
            new Response(response: 200, description: 'OK'),
            new Response(response: 401, description: 'Not allowed')
        ],
    )]
    public function update(Faction $toUpdate, Request $request): JsonResponse
    {
        $changes = json_decode($request->getContent(), true);

        foreach ($changes as $key => $value) {
            switch ($key) {
                case "faction_name":
                    $toUpdate->setFactionName($value);
                    break;
                case "description":
                    $toUpdate->setDescription($value);
                    break;
            }
        }

        $this->entityManager->persist($toUpdate);
        $this->entityManager->flush();

        return $this->json([]);
    }
}
