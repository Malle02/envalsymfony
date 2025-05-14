<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageForm;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/message')]
#[IsGranted('ROLE_USER')]
class MessageController extends AbstractController
{
    #[Route('/', name: 'app_message_index')]
    public function index(MessageRepository $messageRepository): Response
    {
        $user = $this->getUser();
        
        // Récupérer les messages reçus
        $messagesRecus = $messageRepository->findMessagesRecus($user);
        
        // Récupérer les messages envoyés
        $messagesEnvoyes = $messageRepository->findMessagesEnvoyes($user);
        
        return $this->render('message/index.html.twig', [
            'messagesRecus' => $messagesRecus,
            'messagesEnvoyes' => $messagesEnvoyes,
        ]);
    }
    
    #[Route('/nouveau', name: 'app_message_new')]
    public function new(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        $message = new Message();
        $message->setSender($this->getUser());
        $message->setDateHeure(new \DateTime());
        
        // Si l'ID du destinataire est fourni dans l'URL
        $receiverId = $request->query->get('receiver');
        if ($receiverId) {
            $receiver = $userRepository->find($receiverId);
            if ($receiver) {
                $message->setReceiver($receiver);
            }
        }
        
        // Si l'ID du message auquel répondre est fourni dans l'URL
        $replyToId = $request->query->get('reply_to');
        if ($replyToId) {
            $replyTo = $entityManager->getRepository(Message::class)->find($replyToId);
            if ($replyTo) {
                $message->setReplyTo($replyTo);
                $message->setReceiver($replyTo->getSender());
                $message->setTitre('RE: ' . $replyTo->getTitre());
            }
        }
        
        $form = $this->createForm(MessageForm::class, $message, [
            'user' => $this->getUser(),
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($message);
            $entityManager->flush();
            
            $this->addFlash('success', 'Votre message a été envoyé avec succès.');
            
            return $this->redirectToRoute('app_message_index');
        }
        
        return $this->render('message/new.html.twig', [
            'message' => $message,
            'form' => $form,
        ]);
    }
    
    #[Route('/{id}', name: 'app_message_show', methods: ['GET'])]
    public function show(Message $message): Response
    {
        // Vérifier que l'utilisateur est l'expéditeur ou le destinataire
        $user = $this->getUser();
        if ($message->getSender() !== $user && $message->getReceiver() !== $user) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à ce message.');
        }
        
        return $this->render('message/show.html.twig', [
            'message' => $message,
        ]);
    }
}