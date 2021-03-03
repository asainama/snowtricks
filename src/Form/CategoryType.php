<?php

namespace App\Form;

use App\Entity\Category;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use App\DataTransformer\CategoryTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\DataTransformer\CollectionToArrayTransformer;

class CategoryType extends AbstractType
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->addModelTransformer(new CollectionToArrayTransformer(), true)
            ->addModelTransformer(new CategoryTransformer($this->entityManager), true);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('attr', [
            'class' => 'form__group__tag__input'
        ]);
    }

    public function getParent()
    {
        return TextType::class;
    }
}
