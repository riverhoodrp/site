<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HomeController extends AbstractController
{

    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    #[Route('/players-count', name: 'get_player_count')]
    public function getPlayerCount(): JsonResponse
    {
        try {
            $response = $this->client->request('GET', 'http://riverhood.fr:30120/riverhood_framework/players/get/count');
            $data = $response->toArray();
            $playerCount = $data['count'] ?? 0;

            $jsonResponse = new JsonResponse([
                'success' => true,
                'count' => $playerCount,
            ]);

            // Ajouter les en-têtes CORS
            $jsonResponse->headers->set('Access-Control-Allow-Origin', '*');
            $jsonResponse->headers->set('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
            $jsonResponse->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization');

            return $jsonResponse;

        } catch (TransportExceptionInterface|ClientExceptionInterface|DecodingExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface $e) {
            return new JsonResponse([
                'success' => false,
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig');
    }

    #[Route('/reglement', name: 'app_reglement')]
    public function reglement(): Response
    {
        return $this->render('home/reglement.html.twig');
    }

    #[Route('/nous-rejoindre', name: 'join_us')]
    public function join(): Response
    {
        return $this->render('home/join.html.twig');
    }

    #[Route('/a-propos', name: 'app_about')]
    public function about(): Response
    {
        return $this->render('home/about.html.twig');
    }
}
