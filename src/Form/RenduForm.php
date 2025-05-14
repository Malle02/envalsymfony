<?php

namespace App\Form;

use App\Entity\Etape;
use App\Entity\Message;
use App\Entity\Rendu;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


use Vich\UploaderBundle\Form\Type\VichFileType;

class RenduForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fichier', VichFileType::class, [
                'label' => 'Fichier de votre rendu',
                'required' => true,
                'allow_delete' => false,
                'download_uri' => false,
                'asset_helper' => true,
            ])
        ;
    }


     public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Rendu::class,
        ]);
    }

}
