<?php

namespace App\Form;

use App\Entity\Trick;
use App\Form\ImageType;
use App\Form\VideoType;
use App\Form\CategoryType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class TricksType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'mainImage',
                FileType::class,
                [
                    'constraints' => [
                        new Assert\File([
                        'mimeTypesMessage' => 'Le fichier choisi ne correspond pas à un fichier valide',
                        'maxSize' => '1024k',
                        'maxSizeMessage'=> 'Le fichier est trop volumineux ({{ size }} {{ suffix }}). La taille maximale autorisée est {{ limit }} {{ suffix }}',
                        'mimeTypes' => [
                                'image/jpeg',
                                'image/gif',
                                'image/png',
                            ],
                        ]),
                    ],
                    'required' => false,
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
            ->add('name', TextType::class)
            ->add('description', TextareaType::class)
            ->add('categories', CategoryType::class, [
                'constraints' => [
                    new Assert\Count([
                    'min' => 1,
                    'minMessage' => 'Must have at least one value',
                      // also has max and maxMessage just like the Length constraint
                    ]),
                ],
            ])
            ->add('images', CollectionType::class, [
                // 'constraints' => [
                //     new Assert\Count([
                //     'min' => 1,
                //     'minMessage' => 'Must have at least one value',
                //       // also has max and maxMessage just like the Length constraint
                //     ]),
                // ],
                'required' => false,
                'label' => false,
                'mapped' => false,
                'entry_type' => ImageType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                ])
                ->add('videos', CollectionType::class, [
                    'constraints' => [
                        new Assert\Count([
                        'min' => 1,
                        'minMessage' => 'Must have at least one value',
                          // also has max and maxMessage just like the Length constraint
                        ]),
                    ],
                    'label' => false,
                    'entry_type' => VideoType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false
                    ])
                ->add('Valider', SubmitType::class, [
                    'attr' => ['class' => 'btn btn__primary'],
                    ])
    ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
