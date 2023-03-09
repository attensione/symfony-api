<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\History;
use Doctrine\Persistence\ManagerRegistry;

class HistoryController extends AbstractController
{
    #[Route('/exchange/values', name: 'app_history')]
    public function index(ManagerRegistry $doctrine, Request $request): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $data = json_decode($request->getContent(), true);
        $first = $data['first'];
        $second = $data['second'];

        $history = new History();
        $history->setFirstIn($first);
        $history->setSecondIn($second);
        $history->setFirstOut($second);
        $history->setSecondOut($first);

        $entityManager->persist($history);
        $entityManager->flush();

        return $this->json([
            'FirstIn' => $first,
            'SecondIn' => $second,
            'message' => 'Variables store to database!',
        ]);
    }
}
