<?php

namespace App\Controller;



use App\Entity\Sortie;
use App\Form\SearchFormType;
use App\Form\SortieType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

dump($searchForm->getData());
        if($searchForm->isSubmitted() && $searchForm->isValid()){
            // traitement des champs du formulaire
            $sorties = $this->getDoctrine()
                ->getRepository(Sortie::class) // appel du repository et de ses methodes
                ->findBySearch($searchForm->getData());  // on passe les données du formulaire en parametre du tableau

            dump($sorties);
            if ($sorties){

            return $this->render ("sortie/sortie.html.twig", [
                'searchForm'=>$searchForm->createView(),"sorties"=>$sorties]);}
                else{

                    return new Response('aucun resultat');
                }
        }

        return $this->render ("sortie/sortie.html.twig", ["sorties"=>$sorties,
            'searchForm'=>$searchForm->createView()]);
    }


}
