<?php

namespace App\Repository;

use App\Entity\Round;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Round|null find($id, $lockMode = null, $lockVersion = null)
 * @method Round|null findOneBy(array $criteria, array $orderBy = null)
 * @method Round[]    findAll()
 * @method Round[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RoundRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Round::class);
    }

    /**
     * @param $game
     * @param $creator
     * @return Round[] Returns an array of Round objects
     */
    public function findLinesFromOneCreator($game, $creator)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.game = :game')
            ->andWhere('r.creator = :creator')
            ->setParameter('game', $game)
            ->setParameter('creator', $creator)
            ->orderBy('r.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param $game
     * @return Round[] Returns an array of Round objects
     */
    public function findLinesRound1($game)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.game = :game')
            ->andWhere('r.round1winner is null')
            ->setParameter('game', $game)
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @param $game
     * @return Round[] Returns an array of Round objects
     */
    public function findLinesRound2($game)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.game = :game')
            ->andWhere('r.round2winner is null')
            ->setParameter('game', $game)
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @param $game
     * @return Round[] Returns an array of Round objects
     */
    public function findLinesRound3($game)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.game = :game')
            ->andWhere('r.round3winner is null')
            ->setParameter('game', $game)
            ->getQuery()
            ->getResult()
            ;
    }

    // /**
    //  * @return Round[] Returns an array of Round objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Round
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
