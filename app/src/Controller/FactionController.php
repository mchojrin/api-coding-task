<?php

namespace App\Controller;

use App\Entity\Faction;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/factions', name: 'factions_', format: 'json')]
class FactionController extends AbstractController
{

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    #[Route('/', name: 'index', methods: ['GET'])]
    #[Cache(maxage: 3600, public: true, mustRevalidate: true)]
    public function index(): JsonResponse
    {
        return $this->json($this
            ->entityManager
            ->getRepository(Faction::class)
            ->findAll()
        );
    }

    #[Route('/', name: 'create', methods: ['POST'])]
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
    public function delete(Faction $toDelete): JsonResponse
    {
        $this->entityManager->remove($toDelete);
        $this->entityManager->flush();

        return $this->json([]);
    }

    #[Route('/{id}', name: 'update', methods: ['PATCH'])]
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

    #[Route('/{id}', name: 'detail', methods: ['GET'])]
    #[Cache(maxage: 3600, public: true, mustRevalidate: true)]
    public function detail(Faction $faction): JsonResponse
    {
        return $this->json($faction);
    }
}
