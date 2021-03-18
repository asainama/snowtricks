<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\Video;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class TrickFixtures extends Fixture implements DependentFixtureInterface
{
    private $imageFixtures;
    private $imageDirectory;
    private $imageDirectoryUsers;

    public function __construct($imageFixtures, $imageDirectory, $imageDirectoryUsers)
    {
        $this->imageFixtures = $imageFixtures;
        $this->imageDirectory = $imageDirectory;
        $this->imageDirectoryUsers = $imageDirectoryUsers;
    }

    public function load(ObjectManager $manager)
    {
        // $file = new File($this->imageFixtures . DIRECTORY_SEPARATOR . 'tricks');
        $fileSystem = new Filesystem();
        $tricks = [
            0 => [
                'name' => 'mute',
                'description' => 'saisie de la carre frontside de la planche entre les deux pieds avec la main avant',
                'categorie' => 1,
                'videos' => [
                    'https://www.youtube.com/embed/Opg5g4zsiGY'
                ],
                'images' => [
                    'mute.jpg'
                ],
                'mainImage' => 'mute.jpg'
            ],
            1 => [
                'name' => 'stalefish',
                'description' => 'saisie de la carre backside de la planche entre les deux pieds avec la main arrière',
                'categorie' => 1,
                'videos' => [
                    'https://www.youtube.com/embed/f0PyFsOcnIw',
                    'https://www.youtube.com/embed/f9FjhCt_w2U'
                ],
                'images' => [
                    'stalefish.jpg'
                ],
                'mainImage' => 'stalefish.jpg'
            ],
            2 => [
                'name' => 'nose slide',
                'description' => "Le Nose & Tail Slide est un truc super rad, relativement simple car il ne s'agit que d'une variante des Boardslides. Dans ce didacticiel, nous allons décomposer les astuces et vous montrer comment le composer à la maison afin que vous puissiez l'emmener en toute confiance sur la colline.",
                'categorie' => 5,
                'videos' => [
                    'https://www.youtube.com/embed/oAK9mK7wWvw'
                ],
                'images' => [
                    'nose_slide.webp'
                ],
                'mainImage' => 'nose_slide.webp'
            ],
            3 => [
                'name' => 'rotation 360',
                'description' => "une rotation frontside correspond à une rotation orientée vers la carre backside. Cela peut paraître incohérent mais l'origine étant que dans un halfpipe ou une rampe de skateboard, une rotation frontside se déclenche naturellement depuis une position frontside (i.e. l'appui se fait sur la carre frontside), et vice-versa. Ainsi pour un rider qui a une position regular (pied gauche devant), une rotation frontside se fait dans le sens inverse des aiguilles d'une montre",
                'categorie' => 1,
                'videos' => [
                    'https://www.youtube.com/embed/RgS3fpYmd6U'
                ],
                'images' => [
                    'rotation_360.jpg'
                ],
                'mainImage' => 'rotation_360.jpg'
            ],
            4 => [
                'name' => 'Ollie',
                'description' => "Ollies are one of the most essential skills to learn when it comes to Snowboarding. Whether you're hitting park jumps, side hits, urban jib features or freeriding, the ollie is the most efficient way of getting air. Get this skill on lock down and take your riding to new heights!",
                'categorie' => 1,
                'videos' => [
                    'https://www.youtube.com/embed/f1WUSC3HyWU'
                ],
                'images' => [
                    'ollie.jpg'
                ],
                'mainImage' => 'ollie.jpg'
            ],
            5 => [
                'name' => 'Indy Grabs',
                'description' => "Start directly behind the kicker at a point that will enable you to safely land on the table top or just over the knuckle. Re-create a funnel shape with your turns with a focus towards riding straight up the centre of the kicker.",
                'categorie' => 1,
                'videos' => [
                    'https://www.youtube.com/embed/6yA3XqjTh_w'
                ],
                'images' => [
                    'indy_grab.jpg'
                ],
                'mainImage' => 'indy_grab.jpg'
            ],
            6 => [
                'name' => 'Frontside Boardslide',
                'description' => "A counter rotated position is a very awkward feeling when you're first learning it. The best way to learn the movements for a front board, is to do it while sliding down a run. The counter rotation allows you to slide backwards, but look forwards while sliding.",
                'categorie' => 5,
                'videos' => [
                    'https://www.youtube.com/embed/WRjNFodnOHk'
                ],
                'images' => [
                    'frontside_boardslide.jpg'
                ],
                'mainImage' => 'frontside_boardslide.jpg'
            ],
            7 => [
                'name' => 'Alley-oop',
                'description' => "In halfpipe competition, when a rider rotates 180 degrees or more in the uphill direction. This increases the difficulty of a trick because the rider is spinning against their direction of travel.",
                'categorie' => 1,
                'videos' => [
                    'https://www.youtube.com/embed/sl65sMSWrpY'
                ],
                'images' => [
                    'alley-oop.jpg'
                ],
                'mainImage' => 'alley-oop.jpg'
            ],
        ];
        foreach ($tricks as $key => $value) {
            $trick = new Trick();
            $trick->setName($value['name']);
            $trick->setDescription($value['description']);
            /** @var Category $categorie */
            $categorie = $this->getReference('categorie_' . $value['categorie']);
            $trick->addCategory($categorie);
            $fileSystem->copy($this->imageFixtures . DIRECTORY_SEPARATOR . 'tricks'. DIRECTORY_SEPARATOR . $value['mainImage'], $this->imageFixtures . DIRECTORY_SEPARATOR . 'tricks'. DIRECTORY_SEPARATOR .'_'. $value['mainImage'], true);
            $image = new File($this->imageFixtures . DIRECTORY_SEPARATOR . 'tricks'. DIRECTORY_SEPARATOR .'_'. $value['mainImage']);
            $file = md5(uniqid()) . "." . $image->guessExtension();
            $image->move(
                $this->imageDirectory,
                $file
            );
            $img = new Image();
            $img->setPath($file);
            $trick->setMainImage($file);
            foreach ($value['videos'] as $url) {
                $video = new Video();
                $video->setUrl($url);
                $trick->addVideo($video);
            }
            foreach ($value['images'] as $k => $path) {
                $fileSystem->copy($this->imageFixtures . DIRECTORY_SEPARATOR . 'tricks'. DIRECTORY_SEPARATOR . $path, $this->imageFixtures . DIRECTORY_SEPARATOR . 'tricks'. DIRECTORY_SEPARATOR .'_'. $path, true);
                $image = new File($this->imageFixtures . DIRECTORY_SEPARATOR . 'tricks'. DIRECTORY_SEPARATOR .'_'. $path);
                $file = md5(uniqid()) . "." . $image->guessExtension();
                $image->move(
                    $this->imageDirectory,
                    $file
                );
                $img = new Image();
                $img->setPath($file);
                $trick->addImage($img);
            }
            /** @var User $user */
            $user = $this->getReference('user_'. random_int(1, 5));
            $trick->setUser($user);
            $trick->setCreatedAt(new \DateTime('NOW'));
            $manager->persist($trick);
            $this->addReference("trick_$key", $trick);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
            CategoryFixtures::class,
        ];
    }
}
