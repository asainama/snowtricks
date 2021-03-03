<?php

namespace App\DataTransformer;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;

class CategoryTransformer implements DataTransformerInterface
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
        // $value = json_decode($value);
        $values = array_column(json_decode($value, true), 'value');
        // $names = array_unique(array_filter(array_map('trim', explode(',', $value))));
        // dd($names);
        $tags = $this->entityManager->getRepository(Category::class)->findBy([
            // 'name' => $names
            'name' => $values
        ]);
        $newNames = array_diff($values, $tags);

        foreach ($newNames as $name) {
            $tag = new Category();
            $tag->setName($name);
            $tags[] = $tag;
        }
        return $tags;
    }
}
