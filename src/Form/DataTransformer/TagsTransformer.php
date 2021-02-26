<?php

namespace App\Form\DataTransformer;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;

class TagsTransformer implements DataTransformerInterface
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;    
    }
    /**
     * Transforms an object (issue) to a string (number).
     *
     * @param  Issue|null $issue
     * @return string
     */
    public function transform($value): string
    {
        return implode(',', $value);
    }
    /**
     * Transforms a string (number) to an object (issue).
     *
     * @param  string $issueNumber
     * @return Issue|null
     * @throws TransformationFailedException if object (issue) is not found.
     */
    public function reverseTransform($value): array
    {
        $names = array_unique(array_filter(array_map('trim',explode(',', $value))));
        $tags = $this->entityManager->getRepository(CategoryRepository::class)->findBy([
            'name' => $names
        ]);

        $newNames = array_diff($names, $tags);

        foreach($newNames as $name){
            $tag = new Category();
            $tag->setName($name);
            $tags[] = $tag;
        }
        return $tags;
    }
}