<?php

namespace App\Controller;

use App\Entity\Equipment;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Attributes\Delete;
use OpenApi\Attributes\Get;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Parameter;
use OpenApi\Attributes\Patch;
use OpenApi\Attributes\Post;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\RequestBody;
use OpenApi\Attributes\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/equipments', name: 'equipments_', format: 'yaml')]
class EquipmentController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    #[Route('/', name: 'index', methods: ['GET'])]
    #[Cache(maxage: 3600, public: true, mustRevalidate: true)]
    #[Get(
        path: '/equipments',
        operationId: 'listEquipments',
        description: 'List of all equipments in the database',
        summary: 'List of all equipments',
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
                ->getRepository(Equipment::class)
                ->findAll()
        );
    }

    #[Route('/', name: 'create', methods: ['POST'])]
    #[Post(
        path: '/equipments',
        operationId: 'createEquipment',
        description: 'Create a new equipment',
        summary: 'Create a new equipment',
        requestBody: new RequestBody(
            required: true,
            content: new JsonContent(
                required: ["name", "type", "made_by"],
                properties: [
                    new Property(property: "name", type: "string", example: "Hammer"),
                    new Property(property: "made_by", type: "string", example: "Mike the maker"),
                    new Property(property: "type", type: "string", example: "Tools"),
                ],
            )
        ),
        responses: [
            new Response(response: 200, description: 'OK'),
            new Response(response: 401, description: 'Not allowed')
        ],
    )
    ]
    public function create(Request $request): JsonResponse
    {
        $equipmentData = json_decode($request->getContent(), true);

        $newEquipment = new Equipment(
            name: $equipmentData['name'],
            type: $equipmentData['type'],
            made_by: $equipmentData['made_by'],
        );

        $this->entityManager->persist($newEquipment);
        $this->entityManager->flush();

        return $this->json(
            [
                'id' => $newEquipment->getId(),
            ]);
    }

    #[Route('/{id}', name: 'detail', methods: ['GET'])]
    #[Cache(maxage: 3600, public: true, mustRevalidate: true)]
    #[Get(
        path: '/equipments/{id}',
        operationId: 'getEquipment',
        description: 'Create a new equipment',
        summary: 'Create a new equipment',
        parameters: [
            new Parameter(name: "id", description: 'Equipment id', in: 'path', required: true, example: "1"),
        ],
        responses: [
            new Response(response: 200, description: 'OK'),
            new Response(response: 401, description: 'Not allowed')
        ]
    )
    ]
    public function detail(Equipment $equipment): JsonResponse
    {
        return $this->json($equipment);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    #[Delete(
        path: '/equipments/{id}',
        operationId: 'deleteEquipment',
        description: 'Delete an equipment',
        summary: 'Delete a new equipment',
        parameters: [
            new Parameter(name: "id", description: 'Equipment id', in: 'path', required: true, example: "1"),
        ],
        responses: [
            new Response(response: 200, description: 'OK'),
            new Response(response: 401, description: 'Not allowed')
        ],
    )
    ]
    public function delete(Equipment $toDelete): JsonResponse
    {
        $this->entityManager->remove($toDelete);
        $this->entityManager->flush();

        return $this->json([]);
    }

    #[Route('/{id}', name: 'update', methods: ['PATCH'])]
    #[Patch(
        path: '/equipments/{id}',
        operationId: 'updateEquipment',
        description: 'Update an equipment',
        summary: 'Update an equipment',
        requestBody: new RequestBody(
            required: true,
            content: new JsonContent(
                required: [],
                properties: [
                    new Property(property: "name", type: "string", example: "Hammer"),
                    new Property(property: "made_by", type: "string", example: "Mike the maker"),
                    new Property(property: "type", type: "string", example: "Tools"),
                ],
            )
        ),
        parameters: [
            new Parameter(name: "id", description: 'Equipment id', in: 'path', required: true, example: "1"),
        ],
        responses: [
            new Response(response: 200, description: 'OK'),
            new Response(response: 401, description: 'Not allowed')
        ],
    )]
    public function update(Equipment $toUpdate, Request $request): JsonResponse
    {
        $changes = json_decode($request->getContent(), true);

        foreach ($changes as $key => $value) {
            switch ($key) {
                case "name":
                    $toUpdate->setName($value);
                    break;
                case "type":
                    $toUpdate->setType($value);
                    break;
                case "made_by":
                    $toUpdate->setMadeBy($value);
                    break;
            }
        }

        $this->entityManager->persist($toUpdate);
        $this->entityManager->flush();

        return $this->json([]);
    }
}
