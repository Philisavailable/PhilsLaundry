<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LegalsController extends AbstractController
{
    #[Route('/mentions-legales', name: 'show_mentions')]
    public function showMentions(): Response
    {
        return $this->render('legals/show_mentions.html.twig');
    }

    #[Route('/politique-de-confidentialité', name: 'show_politique')]
    public function showPolitique(): Response
    {
        return $this->render('legals/show_politique.html.twig');
    }
}