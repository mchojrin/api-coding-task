<?php

namespace App\Controller;

use App\Entity\Faction;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class FactionController extends AbstractController
{

    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    #[Route('/faction', name: 'faction_list')]
    public function index(): JsonResponse
    {
        return $this->json($this
            ->entityManager
            ->getRepository(Faction::class)
            ->findAll()
        );
    }
}
