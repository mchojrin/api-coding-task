<?php

namespace App\Controller;

use App\Entity\Equipment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/equipments', name: 'equipments_')]
class EquipmentController extends AbstractController
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
                ->getRepository(Equipment::class)
                ->findAll()
        );
    }

    #[Route('/', name: 'create', methods: ['POST'])]
    #[Cache(maxage: 3600, public: true, mustRevalidate: true)]
    public function create(Request $request): JsonResponse
    {
        $requestBody = json_decode($request->getContent(), true);

        $newEquipment = new Equipment(
            name: $requestBody['name'],
            type: $requestBody['type'],
            made_by: $requestBody['made_by'],
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
    public function detail(Equipment $equipment): JsonResponse
    {
        return $this->json($equipment);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Equipment $toDelete): JsonResponse
    {
        $this->entityManager->remove($toDelete);
        $this->entityManager->flush();

        return $this->json([]);
    }

    #[Route('/{id}', name: 'update', methods: ['PATCH'])]
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
