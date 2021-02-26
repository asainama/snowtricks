<?php

namespace App\Form;

use App\Entity\Trick;
use App\Form\ImageType;
use App\Form\VideoType;
use App\Entity\Category;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
            // ->add('categories', EntityType::class, [
            //     'class' => Category::class,
            //     'multiple' => true,
            //     'expanded' => true,
            //     'choice_label' => 'name',
            //     'choice_value' => 'id',
            //     'query_builder' => function(EntityRepository $er){
            //         return $er->createQueryBuilder('c')
            //                 ->orderBy('c.name', 'ASC');
            //     },
            //     'by_reference' => false
            // ])
            ->add('categories',TagsType::class)
            ->add('images', CollectionType::class, [
                'entry_type' => ImageType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false
                ])
                ->add('videos', CollectionType::class, [
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
