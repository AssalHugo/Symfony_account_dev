<?php

namespace App\Form;

use App\Entity\Employe;
use App\Entity\Groupes;
use App\Entity\ResStockageWork;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StockageWorkType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('path')
            ->add('responsables', CollectionType::class, [
                'entry_type' => EntityType::class,
                'entry_options' => [
                    'class' => Employe::class,
                    'choice_label' => function(Employe $employe) {
                        return $employe->getPrenom() . ' ' . $employe->getNom();
                    },
                    'label' => false,
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => 'Responsables',
            ])
            ->add('groupe', EntityType::class, [
                'class' => Groupes::class,
                'choice_label' => 'nom',
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
