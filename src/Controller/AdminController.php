<?php

namespace App\Controller;

use Exception;
use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\Video;
use App\Entity\Category;
use App\Form\TricksType;
use App\Form\EditTrickType;
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
            return $this->redirectToRoute('app_home');
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
        $form = $this->createForm(EditTrickType::class, $trick);
        $form->handleRequest($request);
        return $this->render('home/edit.html.twig', [
            'form' => $form->createView(),
            'trick' => $trick,
            // 'categories' => $categories
        ]);
    }

    /**
     * @Route("/admin/create/image/{id}", name="app_admin_create_image", methods={"POST"})
     */
    public function imageCreate(Trick $trick, Request $request): Response
    {
        try {
            $img = $request->files->get('file');
            $file = md5(uniqid()) . "." . $img->guessExtension();
            $img->move(
                $this->getParameter('images_directory'),
                $file
            );
            $image = new Image();
            $image->setPath($file);
            $trick->addImage($image);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($trick);
            $entityManager->flush();

            return new JsonResponse(['success' => 200, 'file' => $file,'id' => $image->getId()]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Invalid token'], 400);
        }
    }

    /**
     * @Route("/admin/edit/image/{id}", name="app_admin_edit_image", methods={"POST"})
     */
    public function imageEdit(Image $image, Request $request): Response
    {
        try {
            unlink($this->getParameter('images_directory') . '/' . $image->getPath());
            $img = $request->files->get('file');
            $file = md5(uniqid()) . "." . $img->guessExtension();
            $img->move(
                $this->getParameter('images_directory'),
                $file
            );
            $image->setPath($file);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($image);
            $entityManager->flush();

            return new JsonResponse(['success' => 200, 'file' => $file]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Invalid token'], 400);
        }
    }

    /**
     * @Route("/admin/edit/trick/mainImage/{id}", name="app_admin_edit_image_trick", methods={"POST"})
     */
    public function mainImageEdit(Trick $trick, Request $request): Response
    {
        try {
            $img = $request->files->get('file');
            unlink($this->getParameter('images_directory') . '/' . $trick->getMainImage());
            $file = md5(uniqid()) . "." . $img->guessExtension();
            $img->move(
                $this->getParameter('images_directory'),
                $file
            );
            $trick->setMainImage($file);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($trick);
            $entityManager->flush();

            return new JsonResponse(['success' => 200, 'file' => $file]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Invalid token'], 400);
        }
    }

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
     * @Route("/admin/delete/image/{id}", name="app_admin_delete_image", methods={"DELETE"})
     *
     * @return JsonResponse
     */
    public function deleteImage(Image $image, Request $request)
    {
        $data = json_decode($request->getContent(), true);
        if ($this->isCsrfTokenValid('image', $data['_token'])) {
            unlink($this->getParameter('images_directory') . '/' . $image->getPath());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($image);
            $entityManager->flush();
            return new JsonResponse(['success' => 200]);
        }
        return new JsonResponse(['error' => 'Invalid token'], 400);
    }

    /**
     * @Route("/admin/delete/trick/{id}", name="app_admin_delete_trick", methods={"DELETE"})
     *
     * @return JsonResponse
     */
    public function deleteTrick(Trick $trick, Request $request)
    {
        $data = json_decode($request->getContent(), true);
        try {
            if ($this->isCsrfTokenValid('delete', $data['_token'])) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($trick);
                $entityManager->flush();
                return new JsonResponse(['success' => 200]);
            }
            return new JsonResponse(['error' => 'Invalid token'], 400);
        } catch (\Exception $e) {
            dd($e);
        }
    }

    /**
     * @Route("/admin/edit/trick/{id}", name="app_admin_edit_trick", methods={"POST"})
     *
     * @return JsonResponse
     */
    public function editTrick(Trick $trick, Request $request)
    {
        $categories = array_column(json_decode($request->request->get('categorie'), true), 'value');
        $name = $request->request->get('name');
        $description = $request->request->get('description');
        $token = $request->request->get('_token');
        $categoryTrick = [];
        $isDelete = [];
        // $diff = array_merge(array_diff($categoryTrick, $categories), array_diff($categories, $categoryTrick));
        foreach ($trick->getCategories()->getValues() as $category) {
            if (!in_array($category->getName(), $categories)) {
                $isDelete[] = $category->getName();
            }
            $categoryTrick[] = $category->getName();
        }

        try {
            if ($this->isCsrfTokenValid('trick-edit', $token)) {
                $trick->setName($name);
                $trick->setDescription($description);
                foreach ($categories as $categorie) {
                    if (!in_array($categorie, $categoryTrick)) {
                        $val = $this->getDoctrine()->getRepository(Category::class)->findBy(['name' => $categorie])[0];
                        $cat = ($val !== null) ? $val : new Category();
                        $cat->setName($categorie);
                        $trick->addCategory($cat);
                    }
                }
                foreach ($isDelete as $delete) {
                    $cat = $this->getDoctrine()->getRepository(Category::class)->findBy(['name' => $delete])[0];
                    $trick->removeCategory($cat);
                }
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($trick);
                $entityManager->flush();
                return new JsonResponse(['success' => 200,'url' => $this->generateUrl('app_home')]);
            }
            return new JsonResponse(['error' => 'Invalid token'], 400);
        } catch (\Exception $e) {
            dd($e);
        }
    }

    /**
     * @Route("/admin/create/video/{id}", name="app_admin_create_video", methods={"POST"})
     *
     * @return JsonResponse
     */
    public function createVideo(Trick $trick, Request $request)
    {
        $data = json_decode($request->getContent(), true);
        try {
            if ($this->isCsrfTokenValid('video', $data['_token'])) {
                $video = new Video();
                $video->setUrl($data['url']);
                $trick->addVideo($video);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($trick);
                $entityManager->flush();
                return new JsonResponse(['success' => 200, 'url' => $video->getUrl(), 'id' => $video->getId(), 'trick_id' => $trick->getId()]);
            }
            return new JsonResponse(['error' => 'Invalid token'], 400);
        } catch (\Exception $e) {
            dd($e);
        }
    }

    /**
     * @Route("/admin/edit/video/{id}", name="app_admin_edit_video", methods={"POST"})
     *
     * @return JsonResponse
     */
    public function editVideo(Video $video, Request $request)
    {
        $data = json_decode($request->getContent(), true);
        try {
            if ($this->isCsrfTokenValid('video', $data['_token'])) {
                $video->setUrl($data['url']);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($video);
                $entityManager->flush();
                return new JsonResponse(['success' => 200, 'url' => $video->getUrl()]);
            }
            return new JsonResponse(['error' => 'Invalid token'], 400);
        } catch (\Exception $e) {
            dd($e);
        }
    }

    /**
     * @Route("/admin/delete/video/{id}", name="app_admin_delete_video", methods={"DELETE"})
     *
     * @return JsonResponse
     */
    public function deleteVideo(Video $video, Request $request)
    {
        $data = json_decode($request->getContent(), true);
        try {
            if ($this->isCsrfTokenValid('video', $data['_token'])) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($video);
                $entityManager->flush();
                return new JsonResponse(['success' => 200]);
            }
            return new JsonResponse(['error' => 'Invalid token'], 400);
        } catch (\Exception $e) {
            dd($e);
        }
    }
}
