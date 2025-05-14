<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager,ValidatorInterface $validator ): Response
    {
          if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }
        $user = new User();
        $form = $this->createForm(RegistrationForm::class, $user);
        $form->handleRequest($request);


        
        if ($form->isSubmitted()) {
            if (!$form->isValid()) {
                foreach ($form->getErrors(true) as $error) {
                    dump($error->getMessage()); 
                }
            }
            $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $user->getEmail()]);
            if ($existingUser) {
                $this->addFlash('error', 'Un compte avec cet email existe déjà.');
                return $this->redirectToRoute('app_register');
            }

             $plainPassword = $form->get('plainPassword')->getData();
if (!preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[a-z])[A-Za-z\d!@#$%^&*()_+={}\[\]:;"\'<>?,.\/\-]{6,}$/', $plainPassword)) {
    $this->addFlash('error', 'Le mot de passe doit contenir au moins 6 caractères, incluant au moins une lettre majuscule, une lettre minuscule et un chiffre.');
    return $this->redirectToRoute('app_register');
}

 $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
            $user->setRoles(['ROLE_USER']);
        
            $entityManager->persist($user);
            $entityManager->flush();
        
            $this->addFlash('success', 'Votre compte a été créé avec succès !');
        
            return $this->redirectToRoute('app_login');
        }
        

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
