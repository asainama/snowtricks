<?php

namespace App\Form;

use App\Entity\Trick;
use App\Form\ImageType;
use App\Form\VideoType;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class TricksType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('description', TextareaType::class)
            ->add('group', ChoiceType::class, [
                'mapped' => false,
                'choices' => $options['categories'],
                'choice_label' => function (?Category $category) {
                    return $category ? strtoupper($category->getName()) : '';
                },
                'choice_value' => 'id',
            ])
            // ->add('category', EntityType::class, $this->getOptions('Catégorie', 'Catégorie du trick', [
            //     'class' => Category::class,
            //     'choice_label' => 'name'
            // ]))
            ->add('images', CollectionType::class, [
                'entry_type' => ImageType::class,
                'allow_add' => true,
                'allow_delete' => true
            ])
            ->add('videos', CollectionType::class, [
                'entry_type' => VideoType::class,
                'allow_add' => true,
                'allow_delete' => true
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
            'categories' => null,
        ]);
    }
}
