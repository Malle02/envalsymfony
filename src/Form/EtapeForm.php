<?php

namespace App\Form;

use App\Entity\Etape;
use App\Entity\Parcours;
use App\Entity\Ressource;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EtapeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description')
            ->add('consignes')
            ->add('position')
            ->add('parcours', EntityType::class, [
                'class' => Parcours::class,
                'choice_label' => 'id',
            ])
            ->add('ressources', EntityType::class, [
                'class' => Ressource::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Etape::class,
        ]);
    }
}
