<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\Video;
use App\Entity\Category;
use App\Form\TricksType;
use Doctrine\ORM\EntityManager;
use App\Repository\TrickRepository;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
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
        $form = $this->createForm(TricksType::class, $trick);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $trick = $form->getData();
            $trick->setCreatedAt(new \DateTime('NOW'));
            $trick->setUser($this->getUser());
            $image = $form->get('mainImage')->getData();
            $file = md5(uniqid()) . "." . $image->guessExtension();
            $image->move(
                $this->getParameter('images_directory'),
                $file
            );
            $trick->setMainImage($file);
            $images = array_column($form->get('images')->getData(), 'path');
            foreach ($images as $image) {
                $fichier = md5(uniqid()) . '.' . $image->guessExtension();
                $image->move(
                    $this->getParameter('images_directory'),
                    $fichier
                );
                $img = new Image();
                $img->setPath($fichier);
                $trick->addImage($img);
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($trick);
            $em->flush();
            $this->addFlash('success', 'Le trick a bien été crée');
            $this->redirectToRoute('app_home');
        }
        return $this->render('admin/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/admin/edit/{id}", name="app_admin_edit")
     */
    public function edit(Trick $trick, Request $request): Response
    {
        // $categories = $this->getDoctrine()->getRepository(Category::class)->findBy($id);
        // $form = $this->createForm(TricksType::class, $trick);
        // $form->handleRequest($request);
        return $this->render('home/edit.html.twig', [
            // 'form' => $form->createView(),
            'trick' => $trick,
            // 'categories' => $categories
        ]);
    }

    // public function edit(Request $request, ?int $id = 0): Response
    // {
    //     /** @param TrickRepository $repository */
    //     $trick =  $this->getDoctrine()->getRepository(Trick::class)->find($id);
    //     $form = $this->createForm(TricksType::class, $trick);
    //     $form->handleRequest($request);
    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $trick = $form->getData();
    //         $trick->setCreatedAt(new \DateTime('NOW'));
    //         $trick->setUser($this->getUser());
    //         $image = $form->get('mainImage')->getData();
    //         $file = md5(uniqid()) . "." . $image->guessExtension();
    //         $image->move(
    //             $this->getParameter('images_directory'),
    //             $file
    //         );
    //         $trick->setMainImage($file);
    //         $images = array_column($form->get('images')->getData(), 'path');
    //         foreach ($images as $image) {
    //             $fichier = md5(uniqid()) . '.' . $image->guessExtension();
    //             $image->move(
    //                 $this->getParameter('images_directory'),
    //                 $fichier
    //             );
    //             $img = new Image();
    //             $img->setPath($fichier);
    //             $trick->addImage($img);
    //         }
    //         $em = $this->getDoctrine()->getManager();
    //         $em->persist($trick);
    //         $em->flush();
    //         $this->addFlash('success', 'Le trick a bien été modifié');
    //         $this->redirectToRoute('app_home');
    //     }
    //     return $this->render('admin/edit.html.twig', [
    //         'form' => $form->createView(),
    //     ]);
    // }

    /**
     * @Route("/categories.json", name="app_categories_json")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function categoriesJson(Request $request)
    {
        /** @param CategoryRepository $categoryRepository */
        $categoryRepository = $this->getDoctrine()->getRepository(Category::class);
        $categories = $categoryRepository->findAll();
        if ($query = $request->get('value')) {
            $categories = $categoryRepository->search($query);
        }
        // $categories = $categoryRepository->getCategoryJSON();
        return $this->json($categories, 200, [], ['groups' => ['public']]);
    }

    /**
     * @Route("/admin/delete/image/{id}", name="app_delete_images", methods={"DELETE"})
     *
     * @return JsonResponse
     */
    public function deleteImage(Image $image, Request $request)
    {
        $data = json_decode($request->getContent(), true);

        if ($this->isCsrfTokenValid('delete' . $image->getId(), $data['_token'])) {
            unlink($this->getParameter('images_directory') . '/' . $image->getPath());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($image);
            $entityManager->flush();

            return new JsonResponse(['success' => 200]);
        }
        return new JsonResponse(['error' => 'Invalid token'], 400);
    }
}
