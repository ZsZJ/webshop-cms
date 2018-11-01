<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/user")
 */
class UserController extends AbstractController
{
    // FOS Bundle user manager
    private $userManager;

    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @Route("/", name="user_index", methods="GET")
     */
    public function index(): Response
    {
        return $this->render('user/index.html.twig', ['users' => $this->userManager->findUsers()]);
    }

    /**
     * @Route("/new", name="user_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

}
