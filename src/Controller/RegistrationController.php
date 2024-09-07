<?php

namespace App\Controller;

use DateTime;
use App\Entity\Users;
use App\Service\JWTService;
use App\Service\SendEmailService;
use App\Form\RegistrationFormType;
use App\Repository\UsersRepository;
use App\Security\UsersAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationController extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]
    public function register(
        Request $request, 
        UserPasswordHasherInterface $userPasswordHasher, 
        Security $security, 
        UsersRepository $repo, 
        EntityManagerInterface $entityManager, 
        JWTService $jwt, 
        SendEmailService $mail
    ): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('show_dashboard');
        }
        
        $user = new Users();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userCheck = $repo->findOneByEmail($form->get('email')->getData());

            if ($userCheck) {
                $user->setRoles(['ROLE_USER']);
                $user->setCreatedAt(new DateTime());
                $user->setUpdatedAt(new DateTime());
            
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );

                $entityManager->persist($user);
                $entityManager->flush();

                $header = [
                    'typ' => 'JWT',
                    'alg' => 'HS256'
                ];

                $payload = [
                    'user_id' => $user->getId()
                ];

                $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));

                $mail->send(
                    'no-reply@machin.test',
                    $user->getEmail(),
                    'Activation de votre compte PhilsLaundry',
                    'register',
                    compact('user', 'token')
                );

                $this->addFlash('succes', 'Vous êtes bien inscrit, veuillez votre email en cliquant sur le lien reçu');

                return $security->login($user, UsersAuthenticator::class, 'main');
            }

            $this->addFlash('danger', "Vous êtes déjà inscrit");
            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
    
    #[Route('/verification/{token}', name: 'verify_user')]
    public function verifUser(
        $token, 
        JWTService $jwt, 
        UsersRepository $repo, 
        EntityManagerInterface $entityManager
    ): Response
    {
        if($jwt->isValid($token) && !$jwt->isExpired($token) && $jwt->check($token, $this->getParameter('app.jwtsecret'))){
            $payload = $jwt->getPayload($token);
            $user = $repo->find($payload['user_id']);
        }


        if($user && !$user->isVerified()){
            $user->setIsVerified(true);
            $entityManager->flush();

            $this->addFlash('success', 'Utilisateur activé');
            return $this->redirectToRoute('show_dashboard');
        }
        $this->addFlash('danger', 'Le token est invalide ou a expiré');
        return $this->redirectToRoute('app_login');
    }
}
