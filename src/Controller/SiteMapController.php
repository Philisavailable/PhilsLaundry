<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SiteMapController extends AbstractController
{
    #[Route('/plan-du-site', name: 'show_map')]
    public function showMap(): Response
    {
        return $this->render('site_map/show_map.html.twig');
    }
}
