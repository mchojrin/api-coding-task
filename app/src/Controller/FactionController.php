<?php

namespace App\Controller;

use App\Entity\Faction;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class FactionController extends AbstractController
{

    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    #[Route('/faction', name: 'faction_list', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return $this->json($this
            ->entityManager
            ->getRepository(Faction::class)
            ->findAll()
        );
    }

    #[Route('/faction', name: 'create_faction', methods: ['POST'])]
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

    #[Route('/faction/{id}', name: 'delete_faction', methods: ['DELETE'])]
    public function delete(Faction $toDelete): JsonResponse
    {
        $this->entityManager->remove($toDelete);
        $this->entityManager->flush();

        return $this->json([]);
    }

    #[Route('/faction/{id}', name: 'update_faction', methods: ['PATCH'])]
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

    #[Route('/faction/{id}', name: 'a_faction')]
    public function detail(Faction $faction): JsonResponse
    {
        return $this->json($faction);
    }
}
