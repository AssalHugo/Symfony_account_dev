<?php

namespace App\Form;

use App\Entity\Employe;
use App\Entity\Groupes;
use App\Entity\ResStockageWork;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StockageWorkType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('path')
            ->add('responsables', EntityType::class, [
                'class' => Employe::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
            ->add('groupe', EntityType::class, [
                'class' => Groupes::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ResStockageWork::class,
        ]);
    }
}
