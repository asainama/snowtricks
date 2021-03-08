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

    public function __construct($imageFixtures,  $imageDirectory, $imageDirectoryUsers)
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
                    'https://youtu.be/Opg5g4zsiGY'
                ],
                'images' => [
                    'mute.jpg'
                ],
                'mainImage' => [
                    'mute.jpg'
                ]
            ],
            1 => [
                'name' => 'stalefish',
                'description' => 'saisie de la carre backside de la planche entre les deux pieds avec la main arrière',
                'categorie' => 1,
                'videos' => [
                    'https://youtu.be/f0PyFsOcnIw',
                    'https://youtu.be/f9FjhCt_w2U'
                ],
                'images' => [
                    'stalefish.jpg'
                ],
                'mainImage' => [
                    'stalefish.jpg'
                ]
            ],
            3 => [
                'name' => 'nose slide',
                'description' => "Le Nose & Tail Slide est un truc super rad, relativement simple car il ne s'agit que d'une variante des Boardslides. Dans ce didacticiel, nous allons décomposer les astuces et vous montrer comment le composer à la maison afin que vous puissiez l'emmener en toute confiance sur la colline.",
                'categorie' => 5,
                'videos' => [
                    'https://www.youtube.com/watch?v=oAK9mK7wWvw'
                ],
                'images' => [
                    'nose_slide.webp'
                ],
                'mainImage' => [
                    'nose_slide.webp'
                ]
            ],
            4 => [
                'name' => 'rotation 360',
                'description' => "une rotation frontside correspond à une rotation orientée vers la carre backside. Cela peut paraître incohérent mais l'origine étant que dans un halfpipe ou une rampe de skateboard, une rotation frontside se déclenche naturellement depuis une position frontside (i.e. l'appui se fait sur la carre frontside), et vice-versa. Ainsi pour un rider qui a une position regular (pied gauche devant), une rotation frontside se fait dans le sens inverse des aiguilles d'une montre",
                'categorie' => 1,
                'videos' => [
                    'https://www.youtube.com/watch?v=RgS3fpYmd6U'
                ],
                'images' => [
                    'rotation_360.jpg'
                ],
                'mainImage' => [
                    'rotation_360.jpg'
                ]
            ],
            // 4 => [
            //     'name' => 'mute',
            //     'description' => 'saisie de la carre frontside de la planche entre les deux pieds avec la main avant',
            //     'categorie' => 1,
            //     'videos' => [
            //         'https://youtu.be/Opg5g4zsiGY'
            //     ],
            //     'images' => [
            //         'mute.jpg'
            //     ],
            //     'mainImage' => [
            //         'mute.jpg'
            //     ]
            // ],
            // 6 => [
            //     'name' => 'mute',
            //     'description' => 'saisie de la carre frontside de la planche entre les deux pieds avec la main avant',
            //     'categorie' => 1,
            //     'videos' => [
            //         'https://youtu.be/Opg5g4zsiGY'
            //     ],
            //     'images' => [
            //         'mute.jpg'
            //     ],
            //     'mainImage' => [
            //         'mute.jpg'
            //     ]
            // ],
            // 7 => [
            //     'name' => 'mute',
            //     'description' => 'saisie de la carre frontside de la planche entre les deux pieds avec la main avant',
            //     'categorie' => 1,
            //     'videos' => [
            //         'https://youtu.be/Opg5g4zsiGY'
            //     ],
            //     'images' => [
            //         'mute.jpg'
            //     ],
            //     'mainImage' => [
            //         'mute.jpg'
            //     ]
            // ],
            // 8 => [
            //     'name' => 'mute',
            //     'description' => 'saisie de la carre frontside de la planche entre les deux pieds avec la main avant',
            //     'categorie' => 1,
            //     'videos' => [
            //         'https://youtu.be/Opg5g4zsiGY'
            //     ],
            //     'images' => [
            //         'mute.jpg'
            //     ],
            //     'mainImage' => [
            //         'mute.jpg'
            //     ]
            // ],
            // 9 => [
            //     'name' => 'mute',
            //     'description' => 'saisie de la carre frontside de la planche entre les deux pieds avec la main avant',
            //     'categorie' => 1,
            //     'videos' => [
            //         'https://youtu.be/Opg5g4zsiGY'
            //     ],
            //     'images' => [
            //         'mute.jpg'
            //     ],
            //     'mainImage' => [
            //         'mute.jpg'
            //     ]
            // ],
            // 10 => [
            //     'name' => 'mute',
            //     'description' => 'saisie de la carre frontside de la planche entre les deux pieds avec la main avant',
            //     'categorie' => 1,
            //     'videos' => [
            //         'https://youtu.be/Opg5g4zsiGY'
            //     ],
            //     'images' => [
            //         'mute.jpg'
            //     ],
            //     'mainImage' => [
            //         'mute.jpg'
            //     ]
            // ],
        ];
        foreach ($tricks as $key => $value) {
            $trick = new Trick();
            if ($key === 'name') {
                $trick->setName($value[$key]);
            }
            if ($key === 'description') {
                $trick->setDescription($value[$key]);
            }
            if ($key === 'categorie') {
                /** @var Category $categorie */
                $categorie = $this->getReference('categorie_' . $value[$key]);
                $trick->addCategory($categorie);
            }
            if ($key === 'mainImage') {
                print_r($value[$key]);
                $fileSystem->copy($this->imageFixtures . DIRECTORY_SEPARATOR . 'tricks'. DIRECTORY_SEPARATOR . $value[$key], $this->imageFixtures . DIRECTORY_SEPARATOR . 'tricks'. DIRECTORY_SEPARATOR .'_'. $value[$key], true);
                $image = new File($this->imageFixtures . DIRECTORY_SEPARATOR . 'tricks'. DIRECTORY_SEPARATOR .'_'. $value[$key]);
                $file = md5(uniqid()) . "." . $image->guessExtension();
                $image->move(
                    $this->imageDirectory,
                    $file
                );
                $img = new Image();
                $img->setPath($file);
                $trick->setMainImage($file);
            }
            if ($key === 'videos') {
                foreach ($value[$key] as $url) {
                    $video = new Video();
                    $video->setUrl($url);
                    $trick->addVideo($video);
                }
            }
            if ($key === 'images') {
                foreach ($value[$key] as $k => $path) {
                    $fileSystem->copy($this->imageFixtures . DIRECTORY_SEPARATOR . 'tricks'. DIRECTORY_SEPARATOR . $path[$k], $this->imageFixtures . DIRECTORY_SEPARATOR . 'tricks'. DIRECTORY_SEPARATOR .'_'. $path[$k], true);
                    $image = new File($this->imageFixtures . DIRECTORY_SEPARATOR . 'tricks'. DIRECTORY_SEPARATOR .'_'. $path[$k]);
                    $file = md5(uniqid()) . "." . $image->guessExtension();
                    $image->move(
                        $this->imageDirectory,
                        $file
                    );
                    $img = new Image();
                    $img->setPath($file);
                    $trick->addImage($img);
                }
            }
            /** @var User $user */
            $user = $this->getReference('user_1');
            print_r($user);
            $trick->setUser($user);
            $trick->setCreatedAt(new \DateTime('NOW'));
            $manager->persist($trick);
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
