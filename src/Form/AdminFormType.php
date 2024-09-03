<?php 

namespace App\Form;

use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;

class AdminFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $roles = [
            'Client' => 'ROLE_USER',
            'Employé' => 'ROLE_EMPLOYEE',
            'Administrateur' => 'ROLE_ADMIN'
        ];

        $builder
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
                    'placeholder' => 'Nom',
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
            ->add('email', TextType::class, [
                'constraints' => [
                    new Email([
                        'message' => 'Votre mail doit être valide'
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
            ]);

        $data = $builder->getData();

        if (!$data || !$data->getId()) { 
            $builder->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'label' => 'Mot de passe',
                'attr' => [
                    'autocomplete' => 'new-password',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrez votre mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit avoir minimum {{ limit }} caractères',
                        'max' => 4096,
                    ]),
                ],
            ]);
        } else {
            $builder->add('roles', ChoiceType::class, [
                'choices'  => $roles,
                'expanded' => false,
                'multiple' => true,
                'data'     => $data ? $data->getRoles() : ['ROLE_USER'],
                'label'    => 'Rôles',
                'attr'     => ['class' => 'custom-select my-4']
            ]);
        }

        $builder->add('submit', SubmitType::class, [
            'label' => $data && $data->getId() ? 'Modifier' : 'Créer',
            'validate' => false,
            'attr' => [
                'class' => "d-block mx-auto my-3 btn btn-success col-3"
            ]
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
        ]);
    }
}
