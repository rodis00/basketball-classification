<?php

namespace App\Controller;

use App\Service\RankingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RankingController extends AbstractController
{
    #[Route('', name: 'index')]
    public function index(Request $request, RankingService $service): Response
    {
        $result = null;

        if ($request->isMethod('POST')) {
            $json = $request->request->get('json');

            try {
                $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

                $result = $service->calculate($data['mecze']);
            } catch (\Throwable $e) {
                $this->addFlash('error', 'Błędny JSON');
            }
        }

        return $this->render('ranking/index.html.twig', [
            'result' => $result
        ]);
    }
}
