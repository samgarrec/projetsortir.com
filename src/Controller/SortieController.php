<?php

namespace App\Controller;



use App\Entity\Sortie;
use App\Form\SortieType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SortieController extends Controller
{
    /**
     * @Route("/sortie", name="sortie")
     */
    public function shearchSortie (Request $request, EntityManagerInterface $em)
    {
        $sortie = new Sortie();
        $registerForm =$this->createForm(SortieType:: class, $sortie);




        return $this->render ("user/registersortie.html.twig", ["registerForm"=>$registerForm->createView()]);
    }


}
