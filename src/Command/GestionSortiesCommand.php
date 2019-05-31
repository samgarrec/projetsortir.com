<?php

namespace App\Command;
use App\Entity\Sortie;
use Doctrine\ORM\EntityManagerInterface;

use App\Repository\SortieRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GestionSortiesCommand extends Command
{
    protected static $defaultName = 'app:gestion-sorties';


    public function __construct(EntityManagerInterface $em, SortieRepository $sr)
    {
        // best practices recommend to call the parent constructor first and
        // then set your own properties. That wouldn't work in this case
        // because configure() needs the properties set in this constructor


        parent::__construct();
        $this->em = $em;
        $this->sr = $sr;
    }

    protected function configure()
    {
        $this
            ->setDescription('mise a jour des statuts des sorties')
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $etatRepository = $this->em->getRepository('App:Etat');


        $io = new SymfonyStyle($input, $output);
        $toutesLesSorties = $this->sr->findAll();
        foreach ($toutesLesSorties as $sortie) {
            $io->table(['Nom', 'infos sortie', 'date'], [[$sortie->getNom(), $sortie->getInfoSortie(), $sortie->getDateheureDebut()->format('d-m-Y')]]);
            if ($sortie->getDateLimite() <= new \DateTime('now')) {
                $etat = $etatRepository->find(5);
                $sortie->setEtat($etat);
                $this->em->flush($sortie);
            }
                if ($sortie->getDateheureDebut() <= new \DateTime('now - 1 month')) {
                    $etat = $etatRepository->find(6);
                    $sortie->setEtat($etat);
                    $this->em->flush($sortie);

            }
            if ($sortie->getDateheureDebut() == new \DateTime('now')) {
                $etat = $etatRepository->find(4);
                $sortie->setEtat($etat);
                $this->em->flush($sortie);
            }


            if ($input->getOption('option1')) {
                // ...
            }

            $io->success('You have a new command! Now make it your own! Pass --help to see your options.');
        }
    }
}