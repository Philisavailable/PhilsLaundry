<?php

namespace App\Controller;

use App\Entity\Services;
use App\Form\ServicesFormType;
use App\Repository\ServicesRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ServicesController extends AbstractController
{
    #[Route('/services', name: 'show_services')]
    public function showServices(EntityManagerInterface $entityManager): Response
    {
        $services = $entityManager->getRepository(Services::class)->findBy(['deletedAt' => null]);

        return $this->render('services/show_services.html.twig', [
            'services' => $services,
        ]);
    }

    #[Route('/admin/service', name: 'add_services', methods: ['GET', 'POST'])]
    public function addServices(Request $request, ServicesRepository $repo, SluggerInterface $slug): Response
    {

        $service = new Services();

        $form = $this->createForm(ServicesFormType::class, $service)
            ->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $service->setCreatedAt(new DateTime());
            $service->setUpdatedAt(new DateTime());

            $photo = $form->get('photo')->getData();

            if ($photo) {
                $this->handleFile($service, $photo, $slug);
            }
          
            $repo->save($service, true);
            $this->addFlash('success', "Le service a bien été ajouté");

            return $this->redirectToRoute('show_dashboard');
        }
        return $this->render('admin/services.html.twig', [
            'form' => $form->createView(),
            'service' => $service,
            
        ]);
    }
    #[Route('/admin/service-{id}', name: 'update_services', methods: ['GET', 'POST'])]
    public function updateServices(Services $service, Request $request, ServicesRepository $repo, SluggerInterface $slug): Response
    {

        $currentPhoto = $service->getPhoto();

        $form = $this->createForm(ServicesFormType::class, $service, [
            'photo' => $currentPhoto
        ])->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $service->setUpdatedAt(new DateTime());

            $photo = $form->get('photo')->getData();

            /** @var UploadedFile $photo */
            $photo = $form->get('photo')->getData();

            if($photo) {
                $this->handleFile($service, $photo, $slug);
                unlink($this->getParameter('services_dir') . DIRECTORY_SEPARATOR . $currentPhoto);
            }else{
                $service->setPhoto($currentPhoto);
            } //end if($photo)
          
            $repo->save($service, true);
            $this->addFlash('success', "Le service a bien été modifié");

            return $this->redirectToRoute('show_dashboard');
        }
        return $this->render('admin/services.html.twig', [
            'form' => $form->createView(),
            'service' => $service
        ]);
    }

    #[ROUTE('/admin/service/supprimer-{id}', name: 'delete_service', methods: ['GET'])]
    public function softDeleteBenevole(Services $service, ServicesRepository $repo): Response
    {
        $service->setDeletedAt(new DateTime());
        $repo->save($service, true);
        
        $this->addFlash('success', "Le service a bien été supprimé");


        return $this->redirectToRoute('show_dashboard');
    }

    private function handleFile(Services $services, UploadedFile $photo, SluggerInterface $slug)
    {
        $extension = '.' . $photo->guessExtension();

        $safeFilename = $slug->slug(pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME));

        $newFilename = $safeFilename . '-' . uniqid("", true) . $extension;

        try {
            $photo->move($this->getParameter('services_dir'), $newFilename);
            $services->setPhoto($newFilename);
        } catch (FileException $exception) {
        }

    } // end handleFile()
}
