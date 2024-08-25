<?php

namespace App\Form;

use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class UserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder    
            ->add('name')
            ->add('firstName')
            ->add('email')
            ->add('submit', SubmitType::class, [
            'label' => 'Modifier',
            'validate' => false,
            'attr' => [
                'class' => "d-block mx-auto my-3 btn btn-outline-secondary col-3"
            ]
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
        ]);
    }
}
