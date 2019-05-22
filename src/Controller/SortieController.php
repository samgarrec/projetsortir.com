<?php

namespace App\Controller;



use App\Entity\Sortie;
use App\Form\SearchFormType;
use App\Form\SortieType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SortieController extends Controller
{
    /**
     * @Route("/sorties", name="sortie")
     */
    public function searchSortie (Request $request, EntityManagerInterface $em)
    {

        $sorties = $this->getDoctrine()
            ->getRepository(Sortie::class)
            ->findAll();




        $searchForm =$this->createForm(SearchFormType:: class);
        $searchForm->handleRequest ($request);

        return $this->render ("sortie/sortie.html.twig", ["formsearch"=>$sorties]);
    }


}
