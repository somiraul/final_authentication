<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Roles;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class WebsiteController extends AbstractController
{

    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Route("/index", name="index")
     */
    public function index()
    {
        return $this->render('website/index.html.twig');
    }

    public function register()
    {

        $roles = $this->getDoctrine()
                      ->getRepository(Roles::class)
                      ->findAll();

        return $this->render('website/register.html.twig', array('roles' => $roles));
    }

    public function add_user(Request $request, ObjectManager $manager)
    {
        $data = $request->request->all();

        $user = new User();
        $user->setEmail($data['email']);
        $user->setUsername($data['username']);
        $user->setPassword($this->passwordEncoder->encodePassword($user, $data['password']));
        $user->setSecretQuestion($data['secret_question']);
        $user->setSecretAnswer($data['secret_answer']);
        $user->setRoleId($data['role'][0]);

        $manager->persist($user);
        $manager->flush();

        return new Response("SuccessFully added to Database");
    }

    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('website/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }
}
