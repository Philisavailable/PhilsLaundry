<?php

namespace App\Form;

use App\Entity\Orders;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class OrdersAdminFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('status', ChoiceType::class, [
                'label' => 'Selectionner un statut', 
                'choices' => [
                    'En cours' => 'en cours', 
                    'À Récupérer' => 'à récupérer', 
                    'Terminé' => 'terminé'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => "Veuillez renseigner ce champ : {{ value }}"
                    ])
                ]
            ])
            ->add('weight', NumberType::class, [
                'label' => 'Poids de la commande',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide'
                    ]),
                    new GreaterThan([
                        'value' => 0,
                        'message' => 'Le poids doit être supérieur à zéro.',
                    ]),
                    new Type([
                        'type' => 'numeric',
                        'message' => 'Veuillez entrer un nombre valide pour le poids.',
                    ]),
                ],
                'attr' => [
                    'step' => 'any',
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Modifier la commande',
                'validate' => false,
                'attr' => [
                    'class' => "d-block mx-auto my-3 btn btn-success"
                ]
            ])
           
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Orders::class,
        ]);
    }
}
