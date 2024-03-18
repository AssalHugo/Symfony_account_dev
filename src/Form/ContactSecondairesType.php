<?php

namespace App\Form;

use App\Entity\Employe;
use App\Entity\Localisations;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ContactSecondairesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('mail_secondaire', TextType::class, [
                'label' => 'Mail secondaire : ',
                'required' => false,
            ])
            ->add('telephone_secondaire', TextType::class,[
                'label' => 'Téléphone secondaire : ',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Employe::class,
        ]);
    }
}
