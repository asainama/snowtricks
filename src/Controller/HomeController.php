<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Form\TricksType;
use App\Repository\TrickRepository;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(TrickRepository $trickRepository): Response
    {
        return $this->render('home/index.html.twig', [
            // 'tricks' => $tricks
        ]);
    }

    /**
     * @Route("/ajax/{id}", name="app_ajax")
     */
    public function ajax(Trick $trick): Response
    {
        // $categories = $this->getDoctrine()->getRepository(Category::class)->findBy($id);
        // $form = $this->createForm(TricksType::class, $trick);
        // $form->handleRequest($request);
        return $this->render('home/modal.html.twig', [
            // 'form' => $form->createView(),
            'trick' => $trick,
            // 'categories' => $categories
        ]);
    }

    /**
     * @Route("/api/gettricks/{offset?}", methods={"GET"})
     * @param TrickRepository $trickRepository
     * @return ReponseJson
     */
    public function getTricks(TrickRepository $trickRepository, ?int $offset = 0)
    {
        $tricks = $this->getDoctrine()->getRepository(\App\Entity\Trick::class)->findBy([], null, 4, $offset);
        // dd($tricks);
        return $this->json(
            [
            'tricks' => $tricks,
            'total' => $trickRepository->getCountTricks()
        ],
            200,
            [],
            [ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            }, ObjectNormalizer::IGNORED_ATTRIBUTES => ['comments']]
        );
    }
}
