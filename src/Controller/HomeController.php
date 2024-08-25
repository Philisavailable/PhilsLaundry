<?php

namespace App\Controller;

use App\Entity\Services;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'show_home', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $services = $entityManager->getRepository(Services::class)->findBy(['deletedAt' => null]);

        return $this->render('home/show_home.html.twig', [
            'services' => $services,
        ]);
    }
}
