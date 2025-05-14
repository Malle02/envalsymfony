<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


use App\Entity\Parcours;
use App\Entity\Etape;
use App\Entity\Rendu;
use App\Entity\Message;
use App\Repository\ParcoursRepository;
use App\Repository\EtapeRepository;
use App\Repository\RenduRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/parcours')]
class ParcoursEtapesController extends AbstractController
{
    #[Route('/', name: 'app_parcours_list')]
    public function index(ParcoursRepository $parcoursRepository): Response
    {
        $user = $this->getUser();
        
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        
        // Récupére les parcours associés à l'utilisateur
        $parcours = $parcoursRepository->findParcoursForUser($user);
        
        return $this->render('parcours_etapes/index.html.twig', [
            'parcours' => $parcours,
        ]);
    }
    
    #[Route('/{id}', name: 'app_parcours_show')]
    public function show(Parcours $parcours, EtapeRepository $etapeRepository, RenduRepository $renduRepository): Response
    {
        $user = $this->getUser();
        
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        
        // Vérifier si l'utilisateur a accès à ce parcours
        if (!$parcours->getUsers()->contains($user) && !$this->isGranted('ROLE_CONSEILLER')) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à ce parcours.');
        }
        
        // Récupérer les étapes du parcours, ordonnées par position
        $etapes = $etapeRepository->findBy(['parcours' => $parcours], ['position' => 'ASC']);
        
        // Pour chaque étape, récupérer le dernier rendu de l'utilisateur s'il existe
        $dernierRendu = [];
        foreach ($etapes as $etape) {
            $rendu = $renduRepository->findLastRenduByEtapeAndUser($etape, $user);
            $dernierRendu[$etape->getId()] = $rendu;
        }
        
        return $this->render('parcours_etapes/show.html.twig', [
            'parcours' => $parcours,
            'etapes' => $etapes,
            'dernierRendu' => $dernierRendu,
        ]);
    }
    
    #[Route('/{parcours_id}/etape/{etape_id}/rendu', name: 'app_rendu_submit')]
    public function submitRendu(
        Request $request, 
        EntityManagerInterface $entityManager, 
        int $parcours_id, 
        int $etape_id, 
        ParcoursRepository $parcoursRepository,
        EtapeRepository $etapeRepository
    ): Response {
        $user = $this->getUser();
        
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        
        $parcours = $parcoursRepository->find($parcours_id);
        $etape = $etapeRepository->find($etape_id);
        
        if (!$parcours || !$etape) {
            throw $this->createNotFoundException('Parcours ou étape non trouvé.');
        }
        
        // Vérifie si l'utilisateur a accès à ce parcours
        if (!$parcours->getUsers()->contains($user) && !$this->isGranted('ROLE_CONSEILLER')) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à ce parcours.');
        }
        
        // Vérifie si l'étape appartient au parcours
        if ($etape->getParcours()->getId() !== $parcours->getId()) {
            throw $this->createNotFoundException('Cette étape n\'appartient pas au parcours spécifié.');
        }
        
        $rendu = new Rendu();
        $rendu->setUser($user);
        $rendu->setEtape($etape);
        $rendu->setDateHeure(new \DateTime());
        
        $message = new Message();
        $message->setSender($user);
        
        // Trouver un conseiller à qui envoyer le message (par exemple le premier conseiller)
        $conseillers = $entityManager->getRepository('App\Entity\User')->findByRole('ROLE_CONSEILLER');
        if (count($conseillers) > 0) {
            $message->setReceiver($conseillers[0]);
        } else {
            // Si pas de conseiller, envoyer à soi-même
            $message->setReceiver($user);
        }
        
        $message->setDateHeure(new \DateTime());
        
        $formRendu = $this->createForm(RenduForm::class, $rendu);
        $formMessage = $this->createForm(MessageForm::class, $message);
        
        $formRendu->handleRequest($request);
        $formMessage->handleRequest($request);
        
        if ($formRendu->isSubmitted() && $formRendu->isValid()) {
            $entityManager->persist($rendu);
            
            // Si un message est également soumis
            if ($formMessage->isSubmitted() && $formMessage->isValid()) {
                $entityManager->persist($message);
                $rendu->setMessage($message);
            }
            
            $entityManager->flush();
            
            $this->addFlash('success', 'Votre rendu a été soumis avec succès.');
            return $this->redirectToRoute('app_parcours_show', ['id' => $parcours_id]);
        }
        
        return $this->render('parcours_etapes/submit_rendu.html.twig', [
            'parcours' => $parcours,
            'etape' => $etape,
            'formRendu' => $formRendu,
            'formMessage' => $formMessage,
        ]);
    }
}