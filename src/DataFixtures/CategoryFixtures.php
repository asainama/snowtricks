<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $categories = [
            1 => [
                'name' => 'grab',
            ],
            2 => [
                'name' => 'rotation',
            ],
            3 => [
                'name' => 'flip',
            ],
            4 => [
                'name' => 'rotation désaxée',
            ],
            5 => [
                'name' => 'slide',
            ],
            6 => [
                'name' => 'one foot tricks',
            ],
            7 => [
                'name' => 'old school',
            ],
            8 => [
                'name' => 'sauts',
            ]
        ];
        foreach ($categories as $key => $value) {
            $categorie = new Category();
            $categorie->setName($value['name']);
            $manager->persist($categorie);
        }
        $manager->flush();
    }
}
