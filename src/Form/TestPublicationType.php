<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
class TestPublicationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('idhal', CheckboxType::class, [
            'label'    => 'idhal',
            'required' => false,
        ])        
        ->add('orcid', CheckboxType::class, [
            'label'    => 'orcid',
            'required' => false,
        ]);
    }
}
