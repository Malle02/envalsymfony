<?php

namespace App\Form;

use App\Entity\Message;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Repository\UserRepository;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class MessageForm extends AbstractType
{
    private UserRepository $userRepository;
    
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['user'];
        
        // Si le destinataire n'est pas déjà défini, on affiche le champ
        if (!$builder->getData()->getReceiver()) {
            // Si l'utilisateur est un conseiller, il peut envoyer à n'importe quel utilisateur
            if (in_array('ROLE_CONSEILLER', $user->getRoles())) {
                $builder->add('receiver', EntityType::class, [
                    'class' => User::class,
                    'choice_label' => function (User $user) {
                        return $user->getName() . ' ' . $user->getLastname() . ' (' . $user->getEmail() . ')';
                    },
                    'label' => 'Destinataire',
                    'required' => true,
                    'query_builder' => function (UserRepository $ur) use ($user) {
                        return $ur->createQueryBuilder('u')
                            ->where('u.id != :userId')
                            ->setParameter('userId', $user->getId())
                            ->orderBy('u.lastname', 'ASC');
                    },
                ]);
            } else {
                // Si c'est un candidat, il ne peut envoyer qu'aux conseillers
                $builder->add('receiver', EntityType::class, [
                    'class' => User::class,
                    'choice_label' => function (User $user) {
                        return $user->getName() . ' ' . $user->getLastname() . ' (Conseiller)';
                    },
                    'label' => 'Destinataire',
                    'required' => true,
                    'choices' => $this->userRepository->findByRole('ROLE_CONSEILLER'),
                ]);
            }
        }
        
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre du message',
            ])
            ->add('contenu', TextareaType::class, [
                'label' => 'Contenu du message',
                'attr' => ['rows' => 10],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Message::class,
            'user' => null,
        ]);
        
        $resolver->setRequired('user');
    }
}