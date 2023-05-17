<?php

namespace App\Repository;

use App\Entity\Friendship;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Friendship>
 *
 * @method Friendship|null find($id, $lockMode = null, $lockVersion = null)
 * @method Friendship|null findOneBy(array $criteria, array $orderBy = null)
 * @method Friendship[]    findAll()
 * @method Friendship[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FriendshipRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Friendship::class);
    }

    public function save(Friendship $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Friendship $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Friendship[] Returns an array of Friendship objects
     */
    public function findBySender(User $sender, bool $approved = false): array
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.sender = :sender')
            ->andWhere('f.approved = :approved')
            ->setParameter('sender', $sender)
            ->setParameter('approved', $approved)
            ->orderBy('f.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Friendship[] Returns an array of Friendship objects
     */
    public function findByReceiver(User $receiver, bool $approved = false): array
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.receiver = :receiver')
            ->andWhere('f.approved = :approved')
            ->setParameter('receiver', $receiver)
            ->setParameter('approved', $approved)
            ->orderBy('f.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Friendship[] Returns an array of Friendship objects
     */
    public function findByUser(User $user, bool $approved = true): array
    {
        $qb = $this->createQueryBuilder('f');

        return $qb
            ->andWhere($qb->expr()->orX(
                $qb->expr()->eq('f.sender', ':user'),
                $qb->expr()->eq('f.receiver', ':user')))
            ->andWhere('f.approved = :approved')
            ->setParameter('user', $user)
            ->setParameter('approved', $approved)
            ->orderBy('f.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

//    /**
//     * @return Friendship[] Returns an array of Friendship objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Friendship
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
