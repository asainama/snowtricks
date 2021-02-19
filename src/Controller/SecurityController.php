<?php

namespace App\Controller;

use LogicException;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/register", name="app_register", methods={"GET","POST"})
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, TokenGeneratorInterface $token, \Swift_Mailer $mailer): Response
    {
        $form  = $this->createForm(UserType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $plainPassword = $form->get('plainPassword')->getData();
            $user->setPassWord($passwordEncoder->encodePassword($user, $plainPassword));
            $user->setCreatedAt(new \DateTime('NOW'));
            $user->setRoles(['ROLE_USER']);
            $user->setActivationToken($token->generateToken());
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $message = (new \Swift_Message('Activation de votre compte'))
            ->setFrom('ksoulkaiser@gmail.com')
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView(
                    'emails/activation.html.twig',
                    [
                        'token' => $user->getActivationToken()
                    ],
                    'text/html'
                )
            );
            $mailer->send($message);
            $this->addFlash('success', 'User succesfully created');
            return $this->redirectToRoute('app_home');
        }
        return $this->render(
            'security/register.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }

    /**
     * @Route("/login", name="app_login", methods={"GET","POST"})
     */
    public function login(): Response
    {
        return $this->render('security/login.html.twig');
    }

    /**
     * @Route("/logout", name="app_logout", methods={"GET"})
     */
    public function logout(): void
    {
        throw new LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/activation/{token}", name="app_activation", methods={"GET"})
     */
    public function activation($token, UserRepository $userRepository): Response
    {
        $user= $userRepository->findOneBy(['activation_token' => $token]);
        if (!$user) {
            throw $this->createNotFoundException('Cet utilisateur n\'existe pas');
        }
        $user->setActivationToken(null);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        $this->addFlash('success', 'Vous avez bien activé votre compte');

        return $this->redirectToRoute('app_home');
    }
}
