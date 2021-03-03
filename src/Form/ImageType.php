<?php

namespace App\Form;

use App\Entity\Image;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints as Assert;

class ImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'path',
                FileType::class,
                [
                    'constraints' => [
                        new Assert\File([
                        'mimeTypesMessage' => 'Le fichier choisi ne correspond pas à un fichier valide',
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                                'image/jpeg',
                                'image/gif',
                                'image/png',
                            ],
                        ]),
                    ],
                    'required' => true,
                    'data_class' => null,
                    'label' => 'Choisir une image',
                    'multiple' => false,
                    'attr' =>
                    [
                        'accept',
                        'image/x-png,image/gif,image/jpeg,image/jpg'
                    ]
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
