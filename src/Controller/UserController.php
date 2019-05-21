<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ParticipantType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends Controller
{
    /**
     * @Route("/user", name="user")
     */
    public function index()
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
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
}
