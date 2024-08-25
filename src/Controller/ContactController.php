<?php

namespace App\Controller;

use App\Form\ContactFormType;
use App\Service\SendEmailService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'show_contact')]
    public function showContact(Request $request, SendEmailService $mail): Response
    {
        $form = $this->createForm(ContactFormType::class)
            ->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $mail->send(
                $data['email'],
                'contact.philslaundry@a.a',
                'Contact depuis PhilsLaundry',
                'contact',
                [
                    'nom' => $data['nom'], 
                    'message' => $data['message'], 
                ]
            );

            $this->addFlash('success', "Votre demande a bien été envoyée");

            return $this->redirectToRoute('show_contact');
        }
        return $this->render('contact/show_contact.html.twig', [
            'form' => $form,
        ]);
    }
}
