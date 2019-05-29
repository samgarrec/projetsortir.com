<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Form\CancelType;
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
    public function searchSortie(Request $request, EntityManagerInterface $em)
    {
        $sorties = $this->getDoctrine()->getRepository(Sortie::class)->findAllUnderOneMonth();
        $searchForm = $this->createForm(SearchFormType:: class);
        $searchForm->handleRequest($request);

        dump($searchForm->getData());
        if ($searchForm->isSubmitted() && $searchForm->isValid())
        {
            // Traitement des champs du formulaire
            $sorties = $this->getDoctrine()
                // Appel du repository et de ses méthodes
                ->getRepository(Sortie::class)
                // On passe les données du formulaire en paramètre du tableau
                ->findBySearch($searchForm->getData(), $this->getUser());

            if ($sorties)
            {
                $this->addFlash('notice','Resultat de votre recherche');

                return $this->render("sortie/sortie.html.twig", ['searchForm' => $searchForm->createView(), "sorties" => $sorties]);
            } else
            {
                $this->addFlash('notice','Aucun résultat ...');
                $this->redirectToRoute("sortie");
            }
        }
        return $this->render("sortie/sortie.html.twig", ["sorties" => $sorties, 'searchForm' => $searchForm->createView()]);
    }


    /**
     * Fonction permettant de créer une nouvelle sortie
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/user/nouvelle_sortie", name="nouvelle_sortie")
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

            if ($nextAction == 'enregistrer')
            {
                $this->redirectToRoute("nouvelle_sortie");
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
            return $this->redirectToRoute("nouvelle_sortie");
        }
        return $this->render("sortie/registerSortie.html.twig", ["addSortieForm" => $addSortieForm->createView()]);
    }

    /**
     *
     * @Route("/updateSortie/{id}", name="modifierSortie")
     */
    public function updateSortie(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder, Sortie $sortie)
    {
        $addSortieForm = $this->createForm(SortieType:: class, $sortie);
        $addSortieForm->handleRequest($request);

        if ($addSortieForm->isSubmitted() && $addSortieForm->isValid())
        {
            $em->persist($sortie);
            $em->flush();

            $this->addFlash("success", "Vos informations ont bien été enregistrées");
            $this->redirectToRoute("monProfil");

            $this->addFlash("success", "Vos modifications ont bien été prises en compte");
            $this->redirectToRoute("monProfil");
        }

        return $this->render("sortie/registerSortie.html.twig", ["addSortieForm" => $addSortieForm->createView(),"edit" => "edit"]);
    }

    /**
     *
     * @Route("/cancelSortie/{id}", name="annulerSortie")
     */
    public function cancelSortie(Request $request, EntityManagerInterface $em, Sortie $sortie)
    {
        $etatRepository = $em->getRepository('App:Etat');
        $etat = $etatRepository->find(3);
        $cancelForm = $this->createForm(CancelType::class, $sortie);
        $cancelForm->handleRequest($request);

        if ($sortie->getOrganisateur() == $this->getUser())
        {
            if ($cancelForm->isSubmitted() && $cancelForm->isValid())
            {
                $sortie->setInfoSortie('Motif de l\'annulation : '.$sortie->getInfoSortie());
                $sortie->setEtat($etat);
                $em->persist($sortie);
                $em->flush();

                $this->addFlash("notice", "Votre sortie a été annulée");
                return $this->redirectToRoute('sortie');
            }
            return $this->render("sortie/cancelSortie.html.twig", ["cancelForm" => $cancelForm->createView()]);
        }
        return $this->redirectToRoute('sortie');
    }

    /**
     *
     * @Route("/sortie/ajax", name="requeteAjax")
     */
    public function requeteAjax(Request $request, EntityManagerInterface $em)
    {
        $select= $request->request->get('choix');
        $lieux= $em->getRepository(Lieu::class)->findBy(['Ville'=>$select]);
        $lieuTab=[];
        foreach ($lieux as $lieu)
        {
            $lieuTab[$lieu->getId()]=
                [
                'nom' => $lieu->getNom(),
                'rue'=> $lieu->getRue(),
                'lat'=>$lieu->getLatitude(),
                'long'=>$lieu->getLongitude()
                ];
        }
        $response= new Response(json_encode($lieuTab));
        $response->headers->set('Content-Type','application/json');

        return $response;
    }


    /**
     *
     * @Route("/sortie/{id}", name="une-sortie",requirements={"id":"\d+"})
     */
    public function showSortie (Request $request, Sortie $sortie)
    {
        return $this->render ("sortie/uneSortie.html.twig", ["sortie"=>$sortie]);
    }
}