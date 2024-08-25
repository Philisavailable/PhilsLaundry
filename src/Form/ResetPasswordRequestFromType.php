<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ResetPasswordRequestFromType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Veuillez entrer votre adresse e-mail avec laquelle vous vous êtes inscrit chez nous. Nous vous enverrons ensuite un lien que vous pourrez utiliser pour définir un nouveau mot de passe',
                'attr' => [
                    'class' => "my-2",
                    'placeholder' => 'Adresse email',
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Envoyer',
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
            // Configure your form options here
        ]);
    }
}
