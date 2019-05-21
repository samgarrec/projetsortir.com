<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\ParticipantType;
use App\Form\SortieType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends Controller
{
    /**
     * @Route("/", name="login")
     */
    /**
     * on nomme la route login car dans le fichier
     * security.yaml on a login_path: login
     * @Route("/", name="login")
     */
    public function login()
    {
        return $this->render("user/login.html.twig.",
            []);
    }

    /**
     * Symfony gére entierement cette route il suffit de l'appeler logout.
     * Penser à parametre le fichier security.yaml pour rediriger la déconnexion.
     * @Route("/logout", name="logout")
     */
    public function logout()
    {

    }



    /**
     * La fonction register() permet d'enregistrer un nouveau participant dans la BDD
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param UserPasswordEncoderInterface $encoder
     * @Route("/register", name="participant-register")
     */
    public function register (Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder)
    {
        $participant = new Participant();
        $registerForm =$this->createForm(ParticipantType:: class, $participant);
        $registerForm->handleRequest ($request);

        if ($registerForm->isSubmitted() && $registerForm->isValid())
        {
            $hashed=$encoder->encodePassword ($participant, $participant->getPassword());
            $participant->setPassword ($hashed);
            $em->persist($participant);
            $em->flush();
            $this->addFlash("success", "Vos informations ont bien été enregistrées");
            $this->redirectToRoute("sortireni.com");
        }

        return $this->render ("user/register.html.twig", ["registerForm"=>$registerForm->createView()]);
    }


    /**
     *
     * @Route("/registersortie")
     */
    public function registerSortie (Request $request, EntityManagerInterface $em)
    {
        $sortie = new Sortie();
        $registerForm =$this->createForm(SortieType:: class, $sortie);
        $registerForm->handleRequest ($request);

        if ($registerForm->isSubmitted() && $registerForm->isValid())
        {

            $em->persist($sortie);
            $em->flush();
            $this->addFlash("success", "Vos informations ont bien été enregistrées");
            $this->redirectToRoute("sortireni.com");
        }

        return $this->render ("user/registersortie.html.twig", ["registerForm"=>$registerForm->createView()]);
    }



    /**
     *
     * @Route("/profil", name="monProfil")
     */
    public function updateProfile (Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder)
    {
        $participant = $this->getUser();
        $registerForm =$this->createForm(ParticipantType:: class, $participant);
        $registerForm->handleRequest ($request);

        if ($registerForm->isSubmitted() && $registerForm->isValid())
        {
            $hashed=$encoder->encodePassword ($participant, $participant->getPassword());
            $participant->setPassword ($hashed);
            $em->persist($participant);
            $em->flush();
            $this->addFlash("success", "Vos informations ont bien été enregistrées");
            $this->redirectToRoute("sortireni.com");
        }

        return $this->render ("user/profil.html.twig", ["registerForm"=>$registerForm->createView()]);
    }
}
