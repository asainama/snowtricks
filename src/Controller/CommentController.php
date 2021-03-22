<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\Comment;
use App\Repository\CommentRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommentController extends AbstractController
{
    /**
    * @Route("/api/getcomments/{id}/{offset?}", methods={"GET"})
    * @param CommentRepository $commentRepository
    * @return ReponseJson
    */
    public function getComments(CommentRepository $commentRepository, $id = 0, $offset = 0)
    {
        $tricks = $this->getDoctrine()->getRepository(\App\Entity\Trick::class)->findBy(['id' => $id], null, 4, $offset);
        $total = count($this->getDoctrine()->getRepository(\App\Entity\Trick::class)->findBy(['id' => $id])[0]->getComments());
        return $this->json(
            [
                'comments' => $tricks[0]->getComments(),
                'total' => $total
                ],
            200,
            [],
            [ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            }, ObjectNormalizer::IGNORED_ATTRIBUTES => ['trick']]
        );
    }

    /**
    * @Route("/admin/create/comment/{id}", methods={"POST"}, name="app_admin_create_comment")
    * @param CommentRepository $commentRepository
    * @return ReponseJson
    */
    public function createComment(Trick $trick, Request $request)
    {
        $data = json_decode($request->getContent(), true);
        try {
            if ($this->isCsrfTokenValid('trick' . $trick->getId(), $data['_token'])) {
                $user = $this->getUser();
                $comment = new Comment();
                $comment->setContent($data['commentaire']);
                $comment->setUser($user);
                $comment->setCreatedAt(new \DateTime('NOW'));
                $trick->addComment($comment);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($trick);
                $entityManager->flush();
                return new JsonResponse(['success' => 200]);
            }
            return new JsonResponse(['error' => 'Invalid token'], 400);
        } catch (\Exception $e) {
            dd($e);
        }
    }
}
