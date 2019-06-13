<?php
namespace App\Controller;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use OAuth2\OAuth2;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder): JsonResponse
    {


        if ($request->isMethod('POST')) {
            $json = json_decode($request->getContent(false), true);


            $user = new User();
            $alluser= $this->getDoctrine()->getRepository(User::class)->findAll();

            $entrymail = $json["email"];
            $entryusername= $json['nomComplet'];
            $entityManager = $this->getDoctrine()->getManager();
            $userverifymail = $entityManager->getRepository(User::class)->findOneBy(array('email' => $entrymail));
            $userverifyusername= $entityManager->getRepository(User::class)->findOneBy(array('email' => $entryusername));

            if ($userverifymail != null){
                return new JsonResponse(false);
            } else {
                $user->setEmail($json["email"]);
            }

            if ($userverifyusername != null){
                return  new JsonResponse(false);
            } else {
                $user->setNomComplet($json['nomComplet']);
            }

            $user->setPassword($passwordEncoder->encodePassword($user, $json["password"]));
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

        }
        return new JsonResponse(true);
    }
    /**
     * @Route("/forgotten_password", name="app_forgotten_password")
     */
    public function forgottenPassword(
        Request $request,
        \Swift_Mailer $mailer,
        TokenGeneratorInterface $tokenGenerator
    ): Response
    {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $entityManager = $this->getDoctrine()->getManager();
            $user = $entityManager->getRepository(User::class)->findOneByEmail($email);
            /* @var $user User */
            if ($user === null) {
                $this->addFlash('danger', 'Email Inconnu');
                return $this->redirectToRoute('homepage');
            }
            $token = $tokenGenerator->generateToken();
            try{
                $user->setResetToken($token);
                $entityManager->flush();
            } catch (\Exception $e) {
                $this->addFlash('warning', $e->getMessage());
                return $this->redirectToRoute('homepage');
            }
            $url = $this->generateUrl('app_reset_password', array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL);
            $message = (new \Swift_Message('Forgot Password'))
                ->setFrom('g.ponty@dev-web.io')
                ->setTo($user->getEmail())
                ->setBody(
                    "blablabla voici le token pour reseter votre mot de passe : " . $url,
                    'text/html'
                );
            $mailer->send($message);
            $this->addFlash('notice', 'Mail envoyé');
            return $this->redirectToRoute('homepage');
        }
        return $this->render('security/forgotten_password.html.twig');
    }
    /**
     * @Route("/reset_password/{token}", name="app_reset_password")
     */
    public function resetPassword(Request $request, string $token, UserPasswordEncoderInterface $passwordEncoder)
    {
        if ($request->isMethod('POST')) {
            $entityManager = $this->getDoctrine()->getManager();
            $user = $entityManager->getRepository(User::class)->findOneByResetToken($token);
            /* @var $user User */
            if ($user === null) {
                $this->addFlash('danger', 'Token Inconnu');
                return $this->redirectToRoute('homepage');
            }
            $user->setResetToken(null);
            $user->setPassword($passwordEncoder->encodePassword($user, $request->request->get('password')));
            $entityManager->flush();
            $this->addFlash('notice', 'Mot de passe mis à jour');
            return $this->redirectToRoute('homepage');
        }else {
            return $this->render('security/reset_password.html.twig', ['token' => $token]);
        }
    }
    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        return $this->redirectToRoute('homepage');
    }

    /**
     * @Route("/signin")
     */
    public function signin(Request $request,OAuth2 $oauth2, UserPasswordEncoderInterface $passwordEncoder)
    {

        if ($request->isMethod('POST')) {

            $content = json_decode($request->getContent(), true);
            $entityManager = $this->getDoctrine()->getManager();
            $email = json_decode($request->getContent(false), true)['email'];
            $user = $entityManager->getRepository(User::class)->findOneBy(array('email' => $email));

            // @var $user User /

            if ($user === null) {
               // return new JsonResponse("Invalid username " . $request->get("email"), 500);
                return new JsonResponse(false);
            }
            $password = $content['password'];
            $validPassword = $passwordEncoder->isPasswordValid($user, $password);
            if ($validPassword) {
            $request2 = new Request();
            $request2->query->add([
                'client_id' => $this->getParameter('oauth2_client_id'),
                'client_secret' => $this->getParameter('oauth2_client_secret'),
                'grant_type' => 'password',
                'username' => $user->getUsername(),
                'password' => $content['password']
            ]);

            try {
                return new JsonResponse(array_merge(
                    json_decode(
                        $oauth2
                            ->grantAccessToken($request2)
                            ->getContent(), true

                    )
                    , array(
                        'expires_at' => (new \DateTime())->getTimestamp() + $this->getParameter('token_lifetime'),
                        'user_id' => $user->getId(),
                        'email' => $user->getEmail(),
                        'nomComplet' => $user->getNomComplet(),
                    )

                ));

            } catch (OAuth2ServerException $e) {
                return new JsonResponse($e->getHttpResponse());
            }
        } else {
                return new JsonResponse($validPassword);
            }
        }

    }
}