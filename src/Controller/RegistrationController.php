<?php

namespace App\Controller;

use App\Entity\Profile;
use App\Form\UserType;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/registration", name="user_registration")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        // 1) Build the form
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        // 2) Handle the submit (will only happen on POST)
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {

            // 3) Encode the password (you could also do this via Doctrine Listener)
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            $user->setActive(1);
            $user->setRoles(array('ROLE_USER'));

            $profile = new Profile();
            $profile->setUser($user);

            // 4) Save the User & Profile
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->persist($profile);
            $entityManager->flush();

            return $this->redirectToRoute('app_login');
        }

        return $this->render(
            'registration/register.html.twig', 
            array('form' => $form->createView())
        );
    }
}
