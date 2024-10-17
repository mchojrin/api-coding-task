<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Throwable;

class ErrorController extends AbstractController
{
    public function show(Throwable $exception, LoggerInterface $logger): JsonResponse
    {
        return $this->json([
            'message' => $exception->getMessage(),
        ]);
    }
}
