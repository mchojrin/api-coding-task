<?php

namespace App\Controller;

use App\Entity\Equipment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Routing\Annotation\Route;

class EquipmentController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    #[Route('/equipment', name: 'equipment_list')]
    #[Cache(public: true, maxage: 3600, mustRevalidate: true)]
    public function index(): JsonResponse
    {
        return $this->json(
            $this
                ->entityManager
                ->getRepository(Equipment::class)
                ->findAll()
        );
    }

    #[Route('/equipment/{id}', name: 'an_equipment')]
    #[Cache(public: true, maxage: 3600, mustRevalidate: true)]
    public function detail(Equipment $equipment): JsonResponse
    {
        return $this->json($equipment);
    }
}
