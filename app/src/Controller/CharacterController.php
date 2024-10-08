<?php

namespace App\Controller;

use App\Entity\Character;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class CharacterController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    #[Route('/character', name: 'character_list')]
    public function index(): JsonResponse
    {
        return $this->json(
            $this
                ->entityManager
                ->getRepository(Character::class)
                ->findAll()
        );
    }

    #[Route('/character/{id}', name: 'a_character')]
    public function detail(Character $character): JsonResponse
    {
        return $this->json($character);
    }
}
