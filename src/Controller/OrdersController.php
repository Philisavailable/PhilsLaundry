<?php

namespace App\Controller;

use DateTime;
use App\Entity\Orders;
use App\Form\OrdersAdminFormType;
use App\Form\OrdersUsersFormType;
use App\Service\SendEmailService;
use App\Repository\OrdersRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrdersController extends AbstractController
{
    #[Route('users/orders', name: 'add_order')]
    public function addOrder(Request $request, OrdersRepository $repo): Response
    {
        $order = new Orders();

        $form = $this->createForm(OrdersUsersFormType::class, $order)
            ->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $order->setCreatedAt(new DateTime());
            $order->setUpdatedAt(new DateTime());
            $order->setStatus("Commandé");
            $order->setUserId($this->getUser());
            $selectedService = $order->getServiceId(); 

            $servicePrice = $selectedService->getPrice();
            $total = $order->getWeight() * $servicePrice;
            $order->setTotal($total); 

            $repo->save($order, true);
            $this->addFlash('success', "La commande a bien été ajouté");

            return $this->redirectToRoute('show_dashboard');
        }
        return $this->render('users/add_order.html.twig', [
            'form' => $form->createView(),
            'order' => $order
        ]);
    }
      
    #[Route('admin/orders/{id}', name: 'update_order')]
    public function updateOrder(Request $request, Orders $order, OrdersRepository $repo, SendEmailService $mail): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à cette page.');
        }

        $form = $this->createForm(OrdersAdminFormType::class, $order)
        ->handleRequest($request);

        $user = $order->getUserId();
        
        if ($form->isSubmitted() && $form->isValid()) {

            $order->setUpdatedAt(new DateTime());
            $total = $order->getWeight() * $order->getServiceId()->getPrice();
            $order->setTotal($total); 

            $repo->save($order, true);
            $this->addFlash('success', "La commande a bien été modifiée");

            if($order->getStatus() == "à récupérer"){
                $mail->send(
                    'no-reply@machin.test',
                    $user->getEmail(),
                    'Mise à jour de votre commande PhilsLaundry',
                    'order',
                    compact('user')
                );
            }

    
            return $this->redirectToRoute('update_order', ['id' => $order->getId()]);
        }

        return $this->render('admin/update_order.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
            'order' => $order
        ]);
    }

    #[Route('users/commande-{id}', name: 'show_order')]
    public function showOrder(int $id, OrdersRepository $orderRepository): Response
    {
        $order = $orderRepository->find($id);

        return $this->render('users/show_order.html.twig', [
            'order' => $order
        ]);
    }


}
