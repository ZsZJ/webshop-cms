<?php

namespace App\Controller;

use App\Entity\Profile;
use App\Form\ProfileType;
use App\Repository\ProfileRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    /**
     * @Route("/profile", name="profile")
     */
    public function index(Request $request, ProfileRepository $profile)
    {
        $form = $this->createForm(ProfileType::class, $profile->findOneBy(['user' => $this->getUser()]));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('profile');
        }

        return $this->render('profile/index.html.twig', [
            'profile' => $profile,
            'form' => $form->createView(),
        ]);
    }
}
