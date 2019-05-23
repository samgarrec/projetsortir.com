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
     * @Route("/sorties", name="sortie")
     */
    public function searchSortie (Request $request, EntityManagerInterface $em)
    {

        return $this->render('sortie.html.twig', [
            'controller_name' => 'SortieController',
        ]);
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
        if ($addSortieForm->isSubmitted() && $addSortieForm->isValid())
        {
            $nextAction = $addSortieForm->get('enregistrer')->isClicked() ? 'enregistrer' : 'publier';

            if ($nextAction == 'enregistrer') {
                $this->redirectToRoute("nouvelle sortie");
                // Récupération des données de l'utilisateur
                $user=$this->getUser();
                // Récupération de l'id_site
                $site = $user->getSite();
                $sortie->setSite($site);
                // Récupération de l'id_etat
                $etatRepository = $em->getRepository('App:Etat');
                $etat = $etatRepository->find (1);
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
    }
}
