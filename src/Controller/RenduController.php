<?php

namespace App\Controller;

use App\Entity\Rendu;
use App\Form\RenduForm;
use App\Repository\RenduRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/rendu')]
#[IsGranted('ROLE_USER')]
final class RenduController extends AbstractController
{
   #[Route('/', name: 'app_rendu_index')]
    public function index(RenduRepository $renduRepository): Response
    {
        $user = $this->getUser();
        
        // Si c'est un conseiller, il peut voir tous les rendus
        if (in_array('ROLE_CONSEILLER', $user->getRoles())) {
            $rendus = $renduRepository->findAll();
        } else {
            // Sinon, il ne voit que ses propres rendus
            $rendus = $renduRepository->findBy(['user' => $user], ['dateHeure' => 'DESC']);
        }
        
        return $this->render('rendu/index.html.twig', [
            'rendus' => $rendus,
        ]);
    }
    
    #[Route('/{id}', name: 'app_rendu_show', methods: ['GET'])]
    public function show(Rendu $rendu): Response
    {
        $user = $this->getUser();
        
        // Vérifie que l'utilisateur est autorisé à voir ce rendu
        if ($rendu->getUser() !== $user && !in_array('ROLE_CONSEILLER', $user->getRoles())) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à ce rendu.');
        }
        
        return $this->render('rendu/show.html.twig', [
            'rendu' => $rendu,
        ]);
    }
}