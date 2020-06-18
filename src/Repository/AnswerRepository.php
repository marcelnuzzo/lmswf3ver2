<?php

namespace App\Repository;

use App\Entity\Answer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Answer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Answer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Answer[]    findAll()
 * @method Answer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnswerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Answer::class);
    }

    // /**
    //  * @return Answer[] Returns an array of Answer objects
    //  */
    public function findByCorrection($question)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.correction = :val')
            ->andWhere('a.questions = :idQuestion')
            ->setParameter('idQuestion', $question)
            ->setParameter('val', 'vrai"')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findPropo($id)
    {
        return $this->createQueryBuilder('a')
                ->select('a.proposition')
                ->andWhere('a.questions = :id')
                ->setParameter('id', $id)
                ->getQuery()
                ->getResult();
    }

    public function findFirstPropo() {
        return $this->createQueryBuilder('a')
                    ->select('a.id')
                    ->orderBy('a.id', 'ASC')
                    ->setMaxResults(1)
                    ->getQuery()
                    ->getResult()
                    ;
    }

    public function findCountPropo()
    {
        return $this->createQueryBuilder('a')
                ->select('count(a.id)')
                ->getQuery()
                ->getSingleScalarResult()
                ;
    }

    public function findProposition()
    {
        return $this->createQueryBuilder('a')
                ->select('a.proposition')
                ->getQuery()
                ->getResult();
    }

    public function findOnePropo($id)
    {
        return $this->createQueryBuilder('a')
                ->select('a.proposition')
                ->andWhere('a.questions = :id')
                ->setParameter('id', $id)
                ->getQuery()
                ->getResult();
    }

    public function findIdAnswer($id)
    {
        return $this->createQueryBuilder('a')
                ->select('a.id')
                ->andWhere('a.questions = :id')
                ->setParameter('id', $id)
                ->setMaxResults(1)
                ->getQuery()
                ->getResult();
    }

    // /**
    //  * @return Answer[] Returns an array of Answer objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Answer
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
