<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ContactFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide'
                    ]),
                    new Regex([
                        'pattern' => "/^[A-Za-zÀ-ÖØ-öø-ÿ\s\-]+$/",
                        'message' => "Veuillez renseigner des caractères valides"
                    ])
                ],
                'label' => false,
                'attr' => [
                    'placeholder' =>'Nom',
                    'class' => 'my-4'
                ]
            ])
            ->add('email', EmailType::class, [
                'constraints' => [
                    new Email([
                        'message' => 'votre mail doit être valide'
                    ]),
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide'
                    ])
                ],
                'label' => false,
                'attr' => [
                    'placeholder' => 'Email',
                    'class' => 'my-4'
                ]
                ])
            ->add('message', TextareaType::class, [
                'label' => "Votre message"
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Envoyer ma demande',
                'validate' => false,
                'attr' => [
                    'class' => "d-block mx-auto my-3 btn btn-outline-secondary col-4"
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
