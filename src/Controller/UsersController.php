<?php

namespace App\Controller;

use DateTime;
use App\Entity\Users;
use App\Entity\Orders;
use App\Entity\Services;
use App\Form\UserFormType;
use App\Form\AdminFormType;
use App\Repository\OrdersRepository;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UsersController extends AbstractController
{
   
    #[Route('/profil', name: 'show_dashboard', methods: ['GET'])]
    public function showDashboard(Request $request,EntityManagerInterface $entityManager): Response
    {
        $searchTerm = $request->query->get('search', '');

        $orders = $entityManager->getRepository(Orders::class)->findOrdersByClientName($searchTerm);
        $clientsOrders = $entityManager->getRepository(Orders::class)->findBy(['userId' => $this->getUser()]);
        $clients = $entityManager->getRepository(Users::class)->findByRole('ROLE_USER');
        $employees = $entityManager->getRepository(Users::class)->findByRole('ROLE_EMPLOYEE');
        $services = $entityManager->getRepository(Services::class)->findBy(['deletedAt' => null]);

        return $this->render('users/show_dashboard.html.twig', [
            'services' => $services,
            'employees' => $employees,
            'clients' => $clients,
            'clientsOrders' => $clientsOrders,
            'orders' => $orders,
            'searchTerm' => $searchTerm,
        ]);
    }

    #[Route('/admin/employee', name: 'add_employee', methods: ['GET', 'POST'])]
    public function addEmployee(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager)
    {

        $user = new Users();
        $form = $this->createForm(AdminFormType::class, $user)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $existingEmployee = $entityManager->getRepository(Users::class)->findOneBy(['email' => $user->getEmail()]);

        if ($existingEmployee) {
            $this->addFlash('danger', "l'employe existe déjà !");
            return $this->redirectToRoute('show_dashboard');

        } else {
            $user->setRoles(['ROLE_EMPLOYEE']);
            $user->setCreatedAt(new DateTime());
            $user->setUpdatedAt(new DateTime());
        }
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Employé ajouté avec succès !');

            return $this->redirectToRoute('show_dashboard');
        }

        return $this->render('admin/users.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    #[ROUTE('/admin/employee/supprimer-{id}', name: 'delete_user', methods: ['GET'])]
    public function deleteEmployee(Users $user, UsersRepository $repo): Response
    {
        $repo->remove($user, true);
        
        $this->addFlash('success', "L'employe a bien été supprimé");

        return $this->redirectToRoute('show_dashboard');
    }

    #[Route('/admin/user-{id}', name: 'update_user', methods: ['GET', 'POST'])]
    public function updateUser(Users $user, Request $request, EntityManagerInterface $entityManager)
    {

        $form = $this->createForm(AdminFormType::class, $user)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

        
            $user->setUpdatedAt(new DateTime());
            
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'L\'utilisateur a été modifié avec succès !');

            return $this->redirectToRoute('show_dashboard');
        }

        return $this->render('admin/users.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    #[Route('/profil/mettre-a-jour', name: 'update_connected_user', methods: ['GET', 'POST'])]
    public function updateConnectedUser(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user instanceof Users) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à cette page.');
        }

        $form = $this->createForm(UserFormType::class, $user)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setUpdatedAt(new DateTime());

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Votre profil a été mis à jour avec succès !');

            return $this->redirectToRoute('show_dashboard');
        }

        return $this->render('users/update_profile.html.twig', [
            'form' => $form->createView(),
        ]);
    }


}
