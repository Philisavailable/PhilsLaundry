<?php

namespace App\Form;

use App\Entity\Services;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ServicesFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre du service',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide'
                    ])
                ]
            ])
            ->add('photo', FileType::class, [
                'label' => 'Photo',
                'data_class' => null,
                'mapped' => false,
                'attr' => [
                    'value' => $options['photo'] !== null ? $options['photo'] : ''
                ],
                'constraints' => [
                    new Image([
                        'mimeTypes' => ['image/jpeg', 'image/png'],
                    ])
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description du service',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide'
                    ])
                ]
            ])
            ->add('price', NumberType::class, [
                'label' => 'Prix au kilogramme',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide'
                    ])
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => $options['photo'] === null ? 'Créer' : 'Modifier',
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
            'data_class' => Services::class,
            'allow_file_upload' => true,
            'photo' => null,
        ]);
    }
}
