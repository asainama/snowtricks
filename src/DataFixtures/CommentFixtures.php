<?php

namespace App\DataFixtures;

use Faker;
use App\Entity\Comment;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 30; $i++) {
            $faker = Faker\Factory::create('fr_FR');
            $comment = new Comment();
            $comment->setContent($faker->text());
            $comment->setCreatedAt(new \DateTime('NOW'));
            $comment->setUser($this->getReference('user_'. random_int(1, 5)));
            $comment->setTrick($this->getReference('trick_'. random_int(0, 7)));
            $manager->persist($comment);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
            TrickFixtures::class,
        ];
    }
}
