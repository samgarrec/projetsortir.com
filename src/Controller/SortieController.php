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
                ->findBySearch($searchForm->getData(),$this->getUser());  // on passe les données du formulaire en parametre du tableau

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







    /**
     * Fonction permettant de créer une nouvelle sortie
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/nouvelle_sortie", name="nouvelle sortie")
     */
    public function addSortie(Request $request, EntityManagerInterface $em)
    {
        $sortie = new Sortie();
        // Création du formulaire
        $addSortieForm = $this->createForm(SortieType:: class, $sortie);
        // Récupération des données du formulaire
        $addSortieForm->handleRequest($request);

        // Vérification de la validité du formulaire
        if ($addSortieForm->isSubmitted() && $addSortieForm->isValid()) {
            $nextAction = $addSortieForm->get('enregistrer')->isClicked() ? 'enregistrer' : 'publier';

            if ($nextAction == 'enregistrer') {
                $this->redirectToRoute("nouvelle sortie");
                // Récupération des données de l'utilisateur
                $user = $this->getUser();
                // Récupération de l'id_site
                $site = $user->getSite();
                $sortie->setSite($site);
                // Récupération de l'id_etat
                $etatRepository = $em->getRepository('App:Etat');
                $etat = $etatRepository->find(1);
                $sortie->setEtat($etat);
                // Récupération de l'id_organisateur
                $sortie->setOrganisateur($this->getUser());

                $em->persist($sortie);
                $em->flush();
                $this->addFlash("success", "Votre sortie a été créée");

            }
            return $this->redirectToRoute("nouvelle sortie");
        }

        return $this->render("sortie/registerSortie.html.twig", ["addSortieForm" => $addSortieForm->createView()]);
    }}