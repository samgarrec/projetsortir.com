<?php

namespace App\Controller;

use App\Entity\Site;
use App\Form\ImportType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use App\Entity\Participant;

use App\Entity\Sortie;
use App\Form\ChangePasswordType;
use App\Form\ParticipantType;
use App\Form\SortieType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class UserController extends Controller
{
    /**
     * On nomme la route login car dans le fichier
     * security.yaml on a login_path: login
     * @Route("/", name="login")
     */
    public function login()
    {
        // Le service authentication_utils permet de récupérer le nom d'utilisateur
        // et l'erreur dans le cas où le formulaire a déjà été soumis mais était invalide
        // (mauvais mot de passe par exemple)
        $authenticationUtils = $this->get('security.authentication_utils');
        if ($authenticationUtils->getLastAuthenticationError()) {

            $error = 'Identifiant ou mot de passe incorrect';
        } else {

            $error = null;

        }
        return $this->render("user/login.html.twig", ['error' => $error]);
    }

    /**
     * Symfony gère entierement cette route il suffit de l'appeler logout.
     * Penser à paramètrer le fichier security.yaml pour rediriger la déconnexion.
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
     * @Route("/admin/register", name="participant-register")
     */
    public function register(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder)
    {
        $participant = new Participant();
        $registerForm = $this->createForm(ParticipantType:: class, $participant);
        $registerForm->handleRequest($request);

        if ($registerForm->isSubmitted() && $registerForm->isValid()) {
            $hashed = $encoder->encodePassword($participant, $participant->getPassword());
            $participant->setPassword($hashed);
            $em->persist($participant);
            $em->flush();
            $this->addFlash("success", "Vos informations ont bien été enregistrées");
            $this->redirectToRoute("monProfil");
        }

        return $this->render("user/register.html.twig", ["registerForm" => $registerForm->createView()]);
    }


    /**
     *
     * @Route("/registersortie")
     */
    public function registerSortie(Request $request, EntityManagerInterface $em)
    {
        $sortie = new Sortie();
        $registerForm = $this->createForm(SortieType:: class, $sortie);
        $registerForm->handleRequest($request);

        if ($registerForm->isSubmitted() && $registerForm->isValid()) {

            $em->persist($sortie);
            $em->flush();
            $this->addFlash("success", "Vos informations ont bien été enregistrées");
            $this->redirectToRoute("sortireni.com");
        }

        return $this->render("registerSortie.html.twig", ["registerForm" => $registerForm->createView()]);
    }


    /**
     * @Route("/profil", name="monProfil")
     */
    public function updateProfile(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder)
    {

        $participant = $this->getUser();
        $currentAvatar = $participant->getPicture();
        $registerForm = $this->createForm(ParticipantType:: class, $participant);
        $registerForm->handleRequest($request);
        if (!empty($currentAvatar)) {

            $avatarPath = ($this->getParameter('pictures_directory') . DIRECTORY_SEPARATOR . $participant->getPicture());
            //$user->setAvatar(new File($avatarPath));
        }
        if ($registerForm->isSubmitted() && $registerForm->isValid()) {

            $file = $participant->getPicture();
            if (!is_null($file)) {

                $fileName = $this->generateUniqueFileName() . '.' . $file->guessExtension();

                $fileName = md5(uniqid()) . '.' . $file->guessExtension();

                $file->move($this->getParameter('pictures_directory'), $fileName);
                $participant->setPicture($fileName);
            } else {
                $participant->setPicture($currentAvatar);
            }
            $em->persist($participant);
            $em->flush();

            $this->addFlash("success", "Vos informations ont bien été enregistrées");
            $this->redirectToRoute("monProfil");
        }

        return $this->render("user/profil.html.twig", ["registerForm" => $registerForm->createView()]);
    }


    /**
     *
     * @Route("/profil/{id}", name="un-profil",requirements={"id":"\d+"})
     */
    public function showProfile(Participant $p)
    {
        $participant = $p;


        return $this->render("user/unprofil.html.twig", ["unProfil" => $p]);
    }


    /**
     * @return mixed
     * @Route("/inscription/{id}", name="inscription")
     */
    public function inscription(Request $request, EntityManagerInterface $em, Sortie $s)
    {
        $user = $this->getUser();

        // On récupère les données contenues dans le SortieRepository
        $sortieRepository = $this->getDoctrine()->getRepository(Sortie::class);

        $s->addParticipant($user);
        $em->persist($s);
        $em->flush();
        $this->addFlash("notice", "Vos modifications ont bien été prises en compte");

        return $this->redirectToRoute('sortie');
    }


    /**
     * @return mixed
     * @Route("/desistement/{id}", name="desistement")
     */
    public function desistement(Request $request, EntityManagerInterface $em, Sortie $s)
    {
        $user = $this->getUser();

        // On récupère les données contenues dans le SortieRepository
        $sortieRepository = $this->getDoctrine()->getRepository(Sortie::class);

        $s->removeParticipant($user);
        $em->persist($s);
        $em->flush();

        return $this->redirectToRoute('sortie');
    }

    /**
     * @return mixed
     * @Route("/publish/{id}", name="publier")
     */
    public function publish(Request $request, EntityManagerInterface $em, Sortie $s)
    {
        $user = $this->getUser();
        $etatRepository = $em->getRepository('App:Etat');
        $etat = $etatRepository->find(2);
        $s->setEtat($etat);


        $em->persist($s);
        $em->flush();

        return $this->redirectToRoute('sortie');
    }

    /**
     * @return mixed
     * @Route("/resetPassword", name="changePassword")
     */
    public function changeUserPassword(Request $request, UserPasswordEncoderInterface $pwdEncoder)
    {

        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class, $user);
        $form->handleRequest($request);


            $oldPassword = $request->request->get('change_password')['oldPassword'];

            // Si l'ancien mot de passe est bon
            if ($pwdEncoder->isPasswordValid($user, $oldPassword)) {

                $user->setPassword($pwdEncoder->encodePassword($user, $form->get('password')->getData()));

                $em->persist($user);
                $em->flush();

                $this->addFlash('notice', 'Votre mot de passe a bien été modifié !');

                return $this->redirectToRoute('sortie');
            } else
            {
                $form->addError(new FormError('Votre ancien mot de passe est incorrect'));
            }

        return $this->render('user/changePassword.html.twig', ["form" => $form->createView()

        ]);
    }


    /**
     * @Route("/admin/import", name="import")
     */
    public function importAction(EntityManagerInterface $em, Request $request,UserPasswordEncoderInterface $pwdI)
    {
//on recupere le fichier
        $csForm = $this->createForm(ImportType:: class);
        $csForm->handleRequest($request);
        if ($csForm->isSubmitted() && $csForm->isValid()) {
            $file = $csForm['csvFile']->getData();
            if ($file != null) {
                dump($file);
                $fileName = 'participant.csv';

                //on stock le fichier pour ensuite le traiter
                $file->move($this->getParameter('csv_directory'), $fileName);
//traitement du fichier
                $utilisateurs = array(); // Tableau qui va contenir les éléments extraits du fichier CSV
                $ligne = 0;
// Import du
                //$handle = fichier à manipuler
                //$data = ligne du fichier à traiter
                if (($handle = fopen('../var/files/' . $fileName, "r")) !== FALSE) { // Lecture du fichier, à adapter
                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) { // Eléments séparés par un point-virgule, à modifier si necessaire
                        $num = count($data); // Nombre d'éléments sur la ligne traitée
                        $ligne++;
                        for ($c = 0; $c < $num; $c++) {
                            $utilisateurs[$ligne] = array(
                                "nom" => $data[0],
                                "prenom" => $data[1],
                                "email" => $data[2],
                                "telephone" => $data[3],
                                "idSite" => $data[4],
                            );
                        }

                    }
                    fclose($handle);
                }
                $siteRepo = $em->getRepository(Site::class);

                //on ajoute en bdd les utilisateurs du tableau $utilisateurs
                foreach ($utilisateurs as $utilisateur) {
                    $user = new Participant();
                    $hashed = $pwdI->encodePassword($user, "123");
                    $user->setPassword($hashed);
                    $user->setActif(true);
                    $user->setAdministrateur(false);

                    $user->setUsername($utilisateur['prenom'].".".$utilisateur['nom']);
                    $user->setNom($utilisateur['nom']);
                    $user->setPrenom($utilisateur['prenom']);
                    $user->setEmail($utilisateur['email']);
                    $user->setTelephone($utilisateur['telephone']);
                    $user->setSite($siteRepo->find($utilisateur['idSite']));

                    $em->persist($user);

                }
                $em->flush();
                $this ->addFlash('success','La liste a été importée en base');
                return $this->redirectToRoute('sortie');

            }$this->addFlash('danger','Aucune liste');
        }                return $this->render('import/importcsv.html.twig', ["form" => $csForm->createView()]);

    }



}


