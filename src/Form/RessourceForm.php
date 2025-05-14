<?php

namespace App\Form;

use App\Entity\Etape;
use App\Entity\Ressource;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Vich\UploaderBundle\Form\Type\VichFileType;

class RessourceForm extends AbstractType
{
     public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('intitule', TextType::class, [
                'label' => 'Intitulé',
            ])
            ->add('presentation', TextareaType::class, [
                'label' => 'Présentation',
                'required' => false,
            ])
            ->add('support', ChoiceType::class, [
                'label' => 'Support',
                'choices' => [
                    'PDF' => 'PDF',
                    'Vidéo' => 'video',
                    'HTML' => 'html',
                ],
            ])
            ->add('nature', ChoiceType::class, [
                'label' => 'Nature',
                'choices' => [
                    'Formulaire' => 'formulaire',
                    'Guide' => 'guide',
                    'QCM' => 'QCM',
                ],
            ])
            ->add('fichier', VichFileType::class, [
                'label' => 'Fichier',
                'required' => false,
                'allow_delete' => true,
                'download_uri' => true,
                'asset_helper' => true,
            ])
            // Rendre optionnel le champ étapes en ajoutant 'required' => false
            ->add('etapes', EntityType::class, [
                'class' => Etape::class,
                'choice_label' => function(Etape $etape) {
                    return $etape->getDescriptif() . ' (Parcours: ' . $etape->getParcours()->getObjet() . ')';
                },
                'multiple' => true,
                'expanded' => false,
                'required' => false, // Important : rendre le champ optionnel
                'label' => 'Associer à des étapes (optionnel)',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ressource::class,
        ]);
    }
}