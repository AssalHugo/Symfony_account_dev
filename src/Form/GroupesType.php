<?php

namespace App\Form;

use App\Entity\Departement;
use App\Entity\Employe;
use App\Entity\Groupes;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GroupesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('groupes', ChoiceType::class, [
                'choices' => $options['groupes'],
                'choice_label' => function (Groupes $groupes) {
                    return $groupes->getNom();
                },
                'choice_value' => function (Groupes $groupes = null) {
                    return $groupes ? $groupes->getId() : '';
                },
                'multiple' => false,
                'expanded' => false,
                'required' => false,
            ], )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'groupes' => [],
        ]);
    }
}
