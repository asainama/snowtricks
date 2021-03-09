<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'attachment',
                FileType::class,
                [
                    'mapped' => false,
                    'label' => 'Choisir une image',
                    'multiple' => false,
                    'attr' =>
                    [
                        'class' => 'attachment',
                        'accept',
                        'image/x-png,image/gif,image/jpeg,image/jpg'
                    ]
                ]
            )
            ->add('name')
            ->add('email')
            ->add('plainPassword', RepeatedType::class, [
                'mapped' => false,
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => ['label' => 'Password'],
                'second_options' => ['label' => 'Repeat Password'],
                'constraints' => [
                    new NotBlank(
                        null,
                        'Le mot de passe ne doit pas être vide'
                    ),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Le mot de passe trop court. Il devrait avoir 6 ou plus',
                        'max' => 4096,
                        'minMessage' => 'Le mot de passe trop long.',
                    ])
                ]
            ])
            ->add('Valider', SubmitType::class, [
                'attr' => ['class' => 'btn btn__primary'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            // 'csrf_protection' => true,
            // 'csrf_field_name' => '_token',
            // 'csrf_token_id'   => 'user_token',
        ]);
    }
}
