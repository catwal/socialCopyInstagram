<?php

namespace App\Controller;

use App\Entity\Publication;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Vich\UploaderBundle\Form\Type\VichImageType;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $session = $request->getSession();
        $userId = $session->get('userId');
        /** @var User $user */
        $user = $entityManager->getRepository(User::class)->findOneBy(['id' =>$userId]);

        $publications = $entityManager->getRepository(Publication::class)->findAll(['user_id' => $userId]);

        return $this->render('home/home.html.twig', [
            'user' => $user,
            'publications' => $publications
        ]);
    }


    #[Route('/home/add_publication', name: 'add_publication')]
    public function addPublication(Request $request, EntityManagerInterface $entityManager): Response
    {
        $session = $request->getSession();
        $userId = $session->get('userId');
        /** @var User $user */
        $user = $entityManager->getRepository(User::class)->findOneBy(['id'=>$userId]);

        $publication = new Publication();
        $form = $this->createFormBuilder($publication)
            ->add('imageFile', VichImageType::class, [
                'label' => 'Photo de votre publication'
            ])
            ->add('comment', TextareaType::class, [
                'label'=> 'Ajouter un commentaire'
            ])
            ->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $publication->setUser($user);
            $publication->setCreatedAt(new \DateTimeImmutable());
            $entityManager->persist($publication);
            $entityManager->flush();
            return $this->redirectToRoute('app_home');
        }
        return $this->render('home/add_publication.html.twig', [
            'form' => $form->createView()
        ]);

    }

}
