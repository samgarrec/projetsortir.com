<?php

namespace App\Repository;

use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use http\Client\Curl\User;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use Symfony\Bridge\Doctrine\RegistryInterface;


/**
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Sortie::class);
    }



    public function findBySearch($searchForm,$user)
    {


        $qb = $this->createQueryBuilder('s');

        if (isset ($searchForm['Site'])) {
            $siteSortie = $searchForm['Site'];

            $qb->Where("s.site = :site");
            $qb->setParameter("site", $siteSortie);
        }
        if (isset ($searchForm['nomDeLaSortie'])) {
            $nomSortie = $searchForm['nomDeLaSortie'];
            $qb->andWhere("s.nom  LIKE :nom");
            $qb->setParameter("nom", '%' . $nomSortie . '%');
        }
        if (isset ($searchForm['dateDepart'])) {
            $dateDepart = $searchForm['dateDepart'];
            $qb->andWhere("s.dateheureDebut > :dateDepart");
            $qb->setParameter('dateDepart', $dateDepart);
        }
        if (isset ($searchForm['dateFin'])) {
            $dateFin = $searchForm['dateFin'];
            $qb->andWhere("s.dateLimite < :dateFin");
            $qb->setParameter('dateFin', $dateFin);
        }
        if (isset($searchForm['isOrganisateur']) && $searchForm['isOrganisateur'] == true) {

            $qb->andWhere("s.organisateur = :organisateur");
            $qb->setParameter('organisateur', $user);
        }
        if ( isset($searchForm['isRegistred']) && $searchForm['isRegistred']==true) {
            dump($searchForm);
            $qb->join("s.participants","p",'WITH', 'p.id = :inscrit');
            $qb->setParameter('inscrit',$user  );
        }

//        if ( isset($searchForm['isNotRegistred']) && $searchForm['isNotRegistred']==true) {
//
//
//            $dql= $this->createQueryBuilder('s2'); // choisi tout depuis sortie
//            $dql->innerJoin('s2.participants','p2')  /filtre eng ardant/ajoute tous les particiapnts  liés à la sortie
//                ->where($qb->expr()->eq('p2.id',$user)); //
//                $qb->orWhere($qb->expr()->notIn('s.id',$dql->getDQL())); // ajout  de la sous condition not in choisi toute les sortie ou l user n est pas inscrit
//
//        };


//            SELECT * from sortie
//join participant_sortie ps on ps.sortie_id=sortie.id
//join participant p on p.id=ps.participant_id
//where p.id
//not in (select p.id
//        from sortie
//        join participant_sortie ps on ps.sortie_id= sortie.id
//        join participant p on p.id=ps.participant_id where p.id=2)
//          $dql= <<<DQL
//                SELECT s
//                 FROM App\Entity\Sortie s
//                 JOIN s.participants p
//                 WHERE p.id NOT IN (
//                    SELECT s2
//                    FROM  App\Entity\Sortie s2
//                    JOIN s2.participants p
//                    WHERE p.id = :user
//                 )
//          DQL;
//


        $query = $qb->getQuery();
        $result = $query->getResult();
        return $result;



        //            ->andWhere('s.exampleField = :val')
////            ->setParameter('val', $value)
////            ->orderBy('s.id', 'ASC')
////            ->setMaxResults(10)
////            ->getQuery()
////            ->getResult()
////            ;
    }






    // /**
    //  * @return Sortie[] Returns an array of Sortie objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Sortie
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

}