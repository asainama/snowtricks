<?php

namespace App\Controller;

use LogicException;
use App\Entity\User;
use App\Entity\Image;
use App\Form\UserType;
use App\Form\LoginType;
use App\Form\ResetPassType;
use App\Form\ForgotPassType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\UserPassportInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/register", name="app_register", methods={"GET","POST"})
     */
    public function register(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        TokenGeneratorInterface $token,
        \Swift_Mailer $mailer
    ): Response {
        $form  = $this->createForm(UserType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $image = $form->get('attachment')->getData();
            $fichier = md5(uniqid()) . "." . $image->guessExtension();
            $image->move(
                $this->getParameter('images_directory_users'),
                $fichier
            );
            $user->setAttachment($fichier);

            $plainPassword = $form->get('plainPassword')->getData();
            $user->setPassWord($passwordEncoder->encodePassword($user, $plainPassword));
            $user->setCreatedAt(new \DateTime('NOW'));
            $user->setRoles(['ROLE_USER']);
            $user->setResetToken(null);
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
            $this->addFlash('success', 'Votre compte a bien été crée, un email vous a été envoyé');
            return $this->redirectToRoute('app_login');
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
    public function login(Request $request): Response
    {
        $form = $this->createForm(LoginType::class);
        $form->handleRequest($request);

        return $this->render(
            'security/login.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }

    /**
     * @Route("/logout", name="app_logout", methods={"GET"})
     */
    public function logout(): void
    {
        $this->addFlash('success', 'Vous êtes maintenant déconnecter');
        throw new LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/activation/{token}", name="app_activation", methods={"GET"})
     */
    public function activation($token, UserRepository $userRepository): Response
    {
        $user= $userRepository->findOneBy(['activationToken' => $token]);
        if (!$user) {
            throw $this->createNotFoundException('Cet utilisateur n\'existe pas');
        }
        $user->setActivationToken(null);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        $this->addFlash('success', 'Vous avez bien activé votre compte');

        return $this->redirectToRoute('app_login');
    }

    /**
     * @Route("/forgotten", name="app_forgotten_pass", methods={"GET","POST"})
     */
    public function forgottenPass(
        Request $request,
        UserRepository $userRepository,
        \Swift_Mailer $mailer,
        TokenGeneratorInterface $token
    ): Response {
        $form = $this->createForm(ForgotPassType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $user = $userRepository->findOneByEmail($data['email']);

            if (!$user) {
                $this->addFlash('warning', 'Cette adresse n\'existe pas');
                return $this->redirectToRoute('app_login');
            }
            $token = $token->generateToken();
            $user->setResetToken($token);
            try {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
            } catch (\Exception $e) {
                $this->addFlash("error", "Une error est survenue : $e->getMessage()");
                return $this->redirectToRoute('app_login');
            }

            $url = $this->generateUrl(
                'app_reset_password',
                ['token' => $token],
                UrlGeneratorInterface::ABSOLUTE_URL
            );

            $message = (new \Swift_Message('Mot de passe oublié'))
            ->setFrom('no-reply@snowtricks.fr')
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView(
                    'emails/reset_password.html.twig',
                    [
                        'url' => $url
                    ],
                    'text/html'
                )
            );
            $mailer->send($message);

            $this->addFlash('success', 'Un email de réinitialisation vous a été envoyé');

            return $this->redirectToRoute('app_login');
        }

        return $this->render(
            'security/forgotten_password.html.twig',
            [
                    'form' => $form->createView()
            ]
        );
    }

    /**
     * @Route("/reset-pass/{token}", name="app_reset_password")
     */
    public function resetPassword(
        $token,
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder
    ): Response {
        $form = $this->createForm(ResetPassType::class, null, ['token' => $token]);
        $form->handleRequest($request);
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['resetToken' => $token]);

        if (!$user) {
            $this->addFlash('error', 'Token inconnu');
            return $this->redirectToRoute('app_login');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $request->request->get('reset_pass');
            $user->setResetToken(null);
            $password = $passwordEncoder->encodePassword($user, $data['plainPassword']['first']);
            $user->setPassword($password);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Mot de passe modifié avec succès');

            return $this->redirectToRoute('app_login');
        }

        return $this->render(
            'security/reset_password.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }
}
