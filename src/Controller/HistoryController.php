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
        $request_data = json_decode($request->getContent(), true);
        $first = $request_data['first'];
        $second = $request_data['second'];

        $history = new History();
        $history->setFirstIn($first);
        $history->setSecondIn($second);
        $history->setFirstOut($second);
        $history->setSecondOut($first);
        //$history->setCreatedAt();

        $entityManager->persist($history);
        $entityManager->flush();

        return $this->json([
            'FirstIn' => $first,
            'SecondIn' => $second,
            'message' => 'Variables store to database!',
        ]);
    }

    #[Route('/exchange/get', name: 'get_app_history')]
    public function getHistory(ManagerRegistry $doctrine, Request $request): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $request_data = json_decode($request->getContent(), true);
        $limit = 5;

        if(!empty($request_data['offset'] !== null)) {
            $offset = $request_data['offset'];
        }
        if(!empty($request_data['filter'])) {
            $filter = $request_data['filter'];
        }

        $history = $entityManager->getRepository(History::class)->findAll();

        $data = [];

        foreach ($history as $item) {
           $data[] = [
               'id' =>  $item->getId(),
               'first_in' => $item->getFirstIn(),
               'second_in' => $item->getSecondIn(),
               'first_out' => $item->getFirstOut(),
               'second_out' => $item->getSecondOut(),
               'create_at' => $item->getCreatedAt(),
               'update_at' => $item->getUpdatedAt(),
           ];
        }

        return $this->json([
            'data' => $data,
            'message' => 'Show variables from database',
        ]);
    }
}
