<?php

namespace App\Controller;

use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    #[Route('/account', name: 'account')]
    public function index(UserRepository $repository, Request $request, ManagerRegistry $manager, UserPasswordHasherInterface $passwordHasher): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $user = $repository->findOneBy(['id' => $this->getUser()->getId()]);

        $form = $this->createForm(UserType::class, $user);

        $originalPassword = $user->getPassword();

        $form->add('password', RepeatedType::class, [
            'required' => false,
            'type' => PasswordType::class,
            'first_options'  => ['label' => 'Mot de passe'],
            'second_options' => ['label' => 'Confirmation du mot de passe'],
            'empty_data' => '',
            'mapped' => false
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $manager->getManager();

            if(!empty($form['password']->getData())) {
                $user->setPassword($passwordHasher->hashPassword($user, $form['password']->getData()));
            } else {
                $user->setPassword($originalPassword);
            }

            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('account');
        }

        return $this->render('account/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}