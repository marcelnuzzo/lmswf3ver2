<?php

namespace App\Repository;

use App\Entity\Question;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Question|null find($id, $lockMode = null, $lockVersion = null)
 * @method Question|null findOneBy(array $criteria, array $orderBy = null)
 * @method Question[]    findAll()
 * @method Question[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuestionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Question::class);
    }

    public function findByLabel()
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT label FROM question
            
            ';

            $stmt = $conn->prepare($sql);
            $stmt->execute();

            // returns an array of arrays (i.e. a raw data set)
            return $stmt->fetchAll();
    }

    public function findFirstId()
    {
        return $this->createQueryBuilder('q')
                    ->select('q.id')
                    ->orderBy('q.id', 'DESC')
                    ->setMaxResults(1)
                    ->getQuery()
                    ->getResult()
                    ;
    }

    public function findByQId($id)
    {
        return $this->createQueryBuilder('q')
                ->select('q.choice')
                ->andWhere('q.id = :id')
                ->setParameter('id', $id)
                ->getQuery()
                ->getResult();
    }

    public function findLibre()
    {
        return $this->createQueryBuilder('q')
                ->andWhere('q.choice = :choice')
                ->setParameter('choice', 'libre"' )
                ->getQuery()
                ->getResult();
    }

    public function findCountQuestion()
    {
        return $this->createQueryBuilder('q')
                ->select('count(q.id)')
                ->getQuery()
                ->getSingleScalarResult()
                ;
    }

    public function findCountLibre()
    {
        return $this->createQueryBuilder('q')
                ->select('count(q.id)')
                ->andWhere('q.choice = :choice')
                ->setParameter('choice', 'libre"')
                ->getQuery()
                ->getSingleScalarResult()
                ;
    }

    // /**
    //  * @return Question[] Returns an array of Question objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('q.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Question
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
