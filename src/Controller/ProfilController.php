<?php

namespace App\Controller;

use App\Entity\Publication;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Vich\UploaderBundle\Form\Type\VichImageType;


class ProfilController extends AbstractController
{
    #[Route('/profil', name: 'app_profil')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $session = $request->getSession();
        $userId = $session->get('userId');

        /** @var User $user */
        $user = $entityManager->getRepository(User::class)->findOneBy(['id' => $userId]);
        $form = $this->createFormBuilder($user)
            ->add('imageFile', VichImageType::class, [
                'label' => 'Photo de votre profil'
            ])
            ->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $user->setUpdatedAt(new \DateTimeImmutable());
            $entityManager->persist($user);
            $entityManager->flush();
        }

        $publications = $entityManager->getRepository(Publication::class)->findAll(['user_id' => $userId]);
        $nbPublications = count($publications);

        return $this->render('profil/profil.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
            'nbPublications' => $nbPublications
        ]);
    }
}
