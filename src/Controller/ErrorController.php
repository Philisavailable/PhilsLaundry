<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ErrorController extends AbstractController
{
    #[Route('/error/{code}', name: 'error')]
    public function showError(int $code): Response
    {
        $template = 'bundles/TwigBundle/Exception/error.html.twig'; // Par défaut

        if ($code === 403) {
            $template = 'bundles/TwigBundle/Exception/error403.html.twig';
        } elseif ($code === 404) {
            $template = 'bundles/TwigBundle/Exception/error404.html.twig';
        }

        return $this->render($template, [], $code);
    }
}
