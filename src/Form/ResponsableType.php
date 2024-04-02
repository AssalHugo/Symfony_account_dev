<?php

namespace App\Form;

use App\Entity\Departement;
use App\Entity\Employe;
use App\Entity\Groupes;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResponsableType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('responsable', EntityType::class, [
                'class' => Employe::class,
                'choice_label' => function(Employe $employe) {
                    return $employe->getPrenom() . ' ' . $employe->getNom();
                },
                'multiple' => false,
                'expanded' => false,
            ])
            ->add('adjoints', CollectionType::class, [
                'entry_type' => EntityType::class,
                'entry_options' => [
                    'class' => Employe::class,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('e')
                            ->orderBy('e.nom', 'ASC');
                    },
                    'choice_label' => function(Employe $employe) {
                        return $employe->getPrenom() . ' ' . $employe->getNom();
                    },
                    'label' => false,
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => 'Ajouter un adjoint : ',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Groupes::class,
        ]);
    }
}
