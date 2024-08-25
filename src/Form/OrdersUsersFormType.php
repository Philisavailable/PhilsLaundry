<?php

namespace App\Form;

use DateTime;
use App\Entity\Orders;
use App\Entity\Services;
use App\Validator\OrderDate;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\LessThan;

class OrdersUsersFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('serviceId', EntityType::class, [
                'class' => Services::class,
                'label' => "Choisissez un service",
                'choice_label' => 'title',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('s')
                        ->where('s.deletedAt IS NULL')
                    ;
                }
            ])
            ->add('date', DateTimeType::class, [
                'date_widget' => 'single_text',
                'hours' => range(8,20),
                'minutes' => [0, 15, 30, 45],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide',
                    ]),
                    new GreaterThanOrEqual([
                        'value' => new DateTime('today'),
                        'message' => 'La date de la commande ne peut pas être antérieure à aujourd\'hui.',
                    ]),
                    new OrderDate(),
                ],
                'attr' => [
                    'placeholder' => 'Sélectionnez une date',
                ],
            ])
            ->add('weight', NumberType::class, [
                'label' => 'Poids de la commande',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide'
                    ]),
                    new GreaterThan([
                        'value' => 0,
                        'message' => 'Le poids doit être supérieur à 0 kg.',
                    ]),
                    new LessThan([
                        'value' => 8,
                        'message' => 'Le poids doit être inférieur à 8 kg.',

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
            ->add('note', TextareaType::class, [
                'label' => "Commentaire de la commande"
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Créer la commande',
                'validate' => false,
                'attr' => [
                    'class' => "d-block mx-auto my-3 btn btn-success col-3"
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
