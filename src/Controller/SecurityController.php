<?php

namespace App\Controller;

use App\Service\JWTService;
use App\Service\SendEmailService;
use App\Form\ResetPasswordFormType;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\ResetPasswordRequestFromType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityController extends AbstractController
{
    
    #[Route(path: '/connexion', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('show_dashboard');
        }
        
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
    
        $session = $request->getSession();

        $attempts = $session->get('login_attempts', 1);
        
        if ($error) {
            $delay = $attempts;
            sleep($delay);
        
            $session->set('login_attempts', $attempts + 1);
        
            $this->addFlash('danger', 'Identifiants invalides. Veuillez vérifier votre email ou mot de passe.');
        } else {
            $session->remove('login_attempts');
        }
    
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/mot-de-passe-oublie', name: 'forgotten_password')]
    public function forgottenPassword(Request $request, UsersRepository $repo, JWTService $jwt, SendEmailService $mail): Response
    {
        $form = $this->createForm(ResetPasswordRequestFromType::class)
            ->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $user = $repo->findOneByEmail($form->get('email')->getData());

            if($user){
                $header = [
                    'typ' => 'JWT',
                    'alg' => 'HS256'
                ];
    
                $payload = [
                    'user_id' => $user->getId()
                ];
    
                $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));

                $url = $this->generateUrl('reset_password', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

                $mail->send(
                    'no-reply@machin.test',
                    $user->getEmail(),
                    'Récuperation de votre mot de passe PhilsLaundry',
                    'password_reset',
                    compact('user', 'url')
                );

                $this->addFlash('succes', 'Email envoyé avec succès');
                return $this->redirectToRoute('app_login');

            }
            $this->addFlash('danger', 'Un problème est survenue');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/reset_password_request.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/mot-de-passe-oublie/{token}', name: 'reset_password')]
    public function resetPassword($token, JWTService $jwt, UsersRepository $repo, Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        if($jwt->isValid($token) && !$jwt->isExpired($token) && $jwt->check($token, $this->getParameter('app.jwtsecret'))){
            $payload = $jwt->getPayload($token);
            $user = $repo->find($payload['user_id']);

            if($user){
                $form = $this->createForm(ResetPasswordFormType::class)
                    ->handleRequest($request);

                    if($form->isSubmitted() && $form->isValid()){
                        $user->setPassword($passwordHasher->hashPassword($user, $form->get('password')->getData()));
                        $entityManager->flush();

                        $this->addFlash('succes', 'Mot de passe changé avec succès');
                        return $this->redirectToRoute('app_login');
                    }
            }
            return $this->render('security/reset_password.html.twig', [
                'form' => $form->createView()
            ]);
        }
        $this->addFlash('danger', 'Le token est invalide ou a expiré');
        return $this->redirectToRoute('app_login');

    
    }
}
