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
            $qb->setParameter('dateDepart',$dateDepart);
        }
        if (isset ($searchForm['dateFin'])) {
            $dateFin = $searchForm['dateFin'];
            $qb->andWhere("s.dateLimite < :dateFin");
            $qb->setParameter('dateFin',$dateFin);
        }
        if ( isset($searchForm['isOrganisateur']) && $searchForm['isOrganisateur']==true) {

            $qb->andWhere("s.organisateur = :organisateur");
            $qb->setParameter('organisateur',$user  );
        }
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