<?php

namespace App\Form;

use App\Entity\Batiments;
use App\Entity\Employe;
use App\Entity\Localisations;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocalisationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('batiment', EntityType::class, [
                'class' => Batiments::class,
                'choice_label' => 'nom',
                'label' => 'Batiment : '
            ])
            ->add('bureau', TextType::class, [
                'label' => 'Bureau : '
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Localisations::class,
        ]);
    }
}
