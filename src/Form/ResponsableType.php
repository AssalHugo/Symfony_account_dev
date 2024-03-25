<?php

namespace App\Form;

use App\Entity\Departement;
use App\Entity\Employe;
use App\Entity\Groupes;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
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
                'query_builder' => function (EntityRepository $er) use ($options) {
                    $currentGroup = $options['data'];
                    return $er->createQueryBuilder('e')
                        ->leftJoin('e.responsable_de', 'g')
                        ->where('g.responsable IS NULL OR e = :currentResponsable')
                        ->setParameter('currentResponsable', $currentGroup->getResponsable());
                },
            ])
            ->add('adjoints', EntityType::class, [
                'class' => Employe::class,
                'choice_label' => function(Employe $employe) {
                    return $employe->getPrenom() . ' ' . $employe->getNom();
                },
                'multiple' => true,
                'expanded' => true,
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
