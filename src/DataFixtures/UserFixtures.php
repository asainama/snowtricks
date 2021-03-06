<?php

namespace App\DataFixtures;

use Faker;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    /** @var UserPasswordEncoderInterface */
    private $encoder;

    private $imageFixtures;
    private $imageDirectory;
    private $imageDirectoryUsers;

    public function __construct(UserPasswordEncoderInterface $encoder, $imageFixtures, $imageDirectory, $imageDirectoryUsers)
    {
        $this->imageFixtures = $imageFixtures;
        $this->imageDirectory = $imageDirectory;
        $this->imageDirectoryUsers = $imageDirectoryUsers;
        $this->encoder = $encoder;
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function load(ObjectManager $manager)
    {
        $fileSystem = new Filesystem();
        $folders = [
            $this->imageDirectoryUsers,
            $this->imageDirectory,
        ];
        try {
            if ($fileSystem->exists($this->imageDirectoryUsers)) {
                $fileSystem->remove($folders);
                $old = umask(0);
                $fileSystem->mkdir($folders, 0775);
                $fileSystem->chown($folders, "www-data");
                $fileSystem->chgrp($folders, "www-data");
                umask($old);
            }
        } catch (IOExceptionInterface $exception) {
            echo "Error deleting directory at ". $exception->getPath();
        }
        for ($i=1; $i <= 5; $i++) {
            try {
                $fileSystem->copy(
                    $this->imageFixtures . DIRECTORY_SEPARATOR . 'user' . DIRECTORY_SEPARATOR . "snowuser$i.jpg",
                    $this->imageFixtures . DIRECTORY_SEPARATOR . 'user' . DIRECTORY_SEPARATOR . "snowuser$i"."_copy.jpg",
                    true
                );
            } catch (IOExceptionInterface $exception) {
                echo $exception->getMessage();
            }

            $faker = Faker\Factory::create('fr_FR');
            $image = new File($this->imageFixtures . DIRECTORY_SEPARATOR . 'user' . DIRECTORY_SEPARATOR . "snowuser$i"."_copy.jpg");
            $file = md5(uniqid()) . "." . $image->guessExtension();
            $image->move(
                $this->imageDirectoryUsers,
                $file
            );
            $user = new User();
            $user
                ->setAttachment($file)
                ->setName($faker->name())
                ->setEmail(($i === 1) ? 'admin@admin.fr' : $faker->email())
                ->setCreatedAt($faker->dateTime())
                ->setRoles(['ROLE_ADMIN'])
                ->setActivationToken(null)
                ->setResetToken(null)
                ->setPassword($this->encoder->encodePassword($user, 'password'));
            $manager->persist($user);
            $this->addReference("user_$i", $user);
        }
        $manager->flush();
    }
}
