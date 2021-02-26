<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\Video;
use App\Entity\Category;
use App\Form\TricksType;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="app_admin")
     */
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [

        ]);
    }

    /**
     * @Route("/admin/create_trick", name="app_admin_create")
     */
    public function create(Request $request): Response
    {
        /** @param TrickRepository $repository */
        $trick = new Trick();
        $video = new Video();
        $category = new Category();
        $category->setName('test');
        $category2 = new Category();
        $category2->setName('add');
        $video->setUrl('');
        $image = new Image();
        $image->setPath('');
        $trick->addVideo($video);
        $trick->addImage($image);
        $trick->addCategory($category);
        $trick->addCategory($category2);
        $form = $this->createForm(TricksType::class, $trick);
        $form->handleRequest($request);
        return $this->render('admin/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
