<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;


class AccountController extends AbstractController
{
    #[Route('/', name: 'signup_account')]
    public function signupAction(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $plainTextPassword = $form["password"]->getData();
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $plainTextPassword
            );
            $user->setPassword($hashedPassword);
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('signin_account');
        }


        return $this->render('account/signup.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/account/signin', name: 'signin_account')]
    public function signinAction(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $session = $request->getSession();
        $form = $this->createFormBuilder()
            ->add('email', EmailType::class, [
               'constraints' => new NotBlank(),
                ])
            ->add('password', PasswordType::class, [
                'constraints' => new NotBlank()
            ])
            ->getForm();

        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()){
            $userEmail = $form['email']->getData();
            /** @var User $user */
            $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $userEmail]);
            if(!$user){
                return $this->redirectToRoute('signup_account');
            }
            $passwordUser = $form['password']->getData();
            $verifyiedPassword = $passwordHasher->isPasswordValid(
                $user,
                $passwordUser
            );
            if($user && $verifyiedPassword){
                $userId = $user->getId();
                $session->set('userId', $userId);
                return $this->redirectToRoute('app_home');
            } else {
                return $this->redirectToRoute('signup_account');
            }

        }


        return $this->render('account/signin.html.twig', [
            'form' => $form->createView()
        ]);
    }



}
