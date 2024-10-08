<?php

namespace App\Form;

use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', TextType::class, [
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
            ->add('name', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide'
                    ]),
                    new Regex([
                        'pattern' => "/^[A-Za-zÀ-ÖØ-öø-ÿ\-]+$/",
                        'message' => "Veuillez renseigner des caractères valides"
                    ])
                ],
                'label' => false,
                'attr' => [
                    'placeholder' =>'Nom',
                    'class' => 'my-4'
                ]
            ])
            ->add('firstName', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide'
                    ]),
                    new Regex([
                        'pattern' => "/^[A-Za-zÀ-ÖØ-öø-ÿ\-]+$/",
                        'message' => "Veuillez renseigner des caractères valides"
                    ])
                ],
                'label' => false,
                'attr' => [
                    'placeholder' => 'Prénom',
                    'class' => 'my-4'
                ]
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label' => 'En soumettant ce formulaire, j’accepte que mes informations soient utilisées exclusivement dans le cadre de ma demande.',
                'mapped' => false,
                'attr' => [
                    'class' => 'checkbox'
                ],
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter les conditions pour créer un compte.',
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrez votre mot de passe',
                    ]),
                    new Length([
                        'min' => 12,
                        'minMessage' => 'Votre mot de passe doit avoir minimum {{ limit }} caractères',
                    ]),
                    new Regex([
                        'pattern' => "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{12,}$/",
                        'message' => "Votre mot de passe doit comporter au minimum 12 caractères mélangeant les majuscules, les minuscules, des chiffres et des caractères spéciaux"

                    ])
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'S\'inscrire',
                'validate' => false,
                'attr' => [
                    'class' => "d-block mx-auto my-4 btn btn-outline-secondary col-3"
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
