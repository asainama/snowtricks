<?php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    public function getCountComments(?int $id = 0)
    {
        $qb = $this->createQueryBuilder('c');
        $qb->select(
            $qb->expr()->count('c.id')
        );
        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getComments(?int $offset = 0, int $limit = 10, int $id = 0)
    {
        $qb = $this->createQueryBuilder('c');
        $qb
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            // ->andWhere('c.trick_id = :id')
            // ->setParameter('id', $id)
            ;
        return $qb->getQuery()->getArrayResult();
    }
}
