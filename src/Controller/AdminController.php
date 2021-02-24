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
        $repository = $this->getDoctrine()->getRepository(Category::class);
        $categories = $repository->findAll();
        $trick = new Trick();
        $video = new Video();
        $video->setUrl('');
        $image = new Image();
        $image->setPath('');
        $trick->addVideo($video);
        $trick->addImage($image);
        $form = $this->createForm(TricksType::class, $trick, ['categories' => $categories]);
        $form->handleRequest($request);
        return $this->render('admin/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
