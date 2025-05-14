<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/accompagnement')]
#[IsGranted('ROLE_CONSEILLER')]
class AccompagnementController extends AbstractController
{
    #[Route('/', name: 'app_accompagnement_index')]
    public function index(UserRepository $userRepository): Response
    {
        $conseiller = $this->getUser();
        
        // Récupére les candidats accompagnés par ce conseiller
        $candidatsAccompagnes = $conseiller->getAccompagne();
        
        
        // Récupére tous les candidats (rôle USER) non accompagnés par ce conseiller
        $autresCandidats = $userRepository->findCandidatsNonAccompagnes($conseiller);
        
        return $this->render('accompagnement/index.html.twig', [
            'candidatsAccompagnes' => $candidatsAccompagnes,
            'autresCandidats' => $autresCandidats,
        ]);
    }
    
    #[Route('/ajouter/{id}', name: 'app_accompagnement_ajouter')]
    public function ajouter(User $candidat, EntityManagerInterface $entityManager): Response
    {
        $conseiller = $this->getUser();
        
        // Vérifie que le candidat a bien le rôle USER
        if (!in_array('ROLE_USER', $candidat->getRoles()) || in_array('ROLE_CONSEILLER', $candidat->getRoles())) {
            $this->addFlash('error', 'Cet utilisateur n\'est pas un candidat.');
            return $this->redirectToRoute('app_accompagnement_index');
        }
        
        // Vérifie que le candidat n'est pas déjà accompagné par ce conseiller
        if ($conseiller->getAccompagnes()->contains($candidat)) {
            $this->addFlash('error', 'Ce candidat est déjà dans votre liste d\'accompagnement.');
            return $this->redirectToRoute('app_accompagnement_index');
        }
        
        // Ajoute le candidat à la liste d'accompagnement
        $conseiller->addAccompagne($candidat);
        $entityManager->flush();
        
        $this->addFlash('success', 'Le candidat a été ajouté à votre liste d\'accompagnement.');
        
        return $this->redirectToRoute('app_accompagnement_index');
    }
    
    #[Route('/retirer/{id}', name: 'app_accompagnement_retirer')]
    public function retirer(User $candidat, EntityManagerInterface $entityManager): Response
    {
        $conseiller = $this->getUser();
        
        // Vérifie que le candidat est bien accompagné par ce conseiller
        if (!$conseiller->getAccompagnes()->contains($candidat)) {
            $this->addFlash('error', 'Ce candidat n\'est pas dans votre liste d\'accompagnement.');
            return $this->redirectToRoute('app_accompagnement_index');
        }
        
        // Retire le candidat de la liste d'accompagnement
        $conseiller->removeAccompagne($candidat);
        $entityManager->flush();
        
        $this->addFlash('success', 'Le candidat a été retiré de votre liste d\'accompagnement.');
        
        return $this->redirectToRoute('app_accompagnement_index');
    }
}