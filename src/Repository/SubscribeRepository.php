<?php

namespace App\Repository;

use App\Entity\Book;
use App\Entity\Subscribe;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Subscribe>
 *
 * @method Subscribe|null find($id, $lockMode = null, $lockVersion = null)
 * @method Subscribe|null findOneBy(array $criteria, array $orderBy = null)
 * @method Subscribe[]    findAll()
 * @method Subscribe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubscribeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subscribe::class);
    }

    public function save(Subscribe $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Subscribe $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Book[] Returns an array of Book objects
     */
    public function getUserSubscribes(User $user): array
    {
        $qb = $this->createQueryBuilder('f');

        return $qb
            ->andWhere($qb->expr()->eq('f.user', ':user'))
            ->setParameter('user', $user)
            ->orderBy('f.timeStamp', 'ASC')
            ->getQuery()
            ->setMaxResults(5)
            ->getResult()
        ;
    }

    /**
     * @return User[] Returns an array of Book objects
     */
    public function getBookSubscribes(Book $book): array
    {
        $qb = $this->createQueryBuilder('f');

        return $qb
            ->andWhere($qb->expr()->eq('f.book', ':book'))
            ->setParameter('book', $book)
            ->orderBy('f.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function getSubscribersByBookId(int $bookId): array
    {
        $qb = $this->createQueryBuilder('f');
        $qb->select('f')
            ->join('f.book', 'b')
            ->where('b.id = :bookId')
            ->setParameter('bookId', $bookId)
            ->orderBy('f.id', 'DESC')
            ->setMaxResults(5);

        return $qb->getQuery()->getResult();
    }

    public function isSubscribed(int $userId, int $bookId): bool
    {
        $qb = $this->createQueryBuilder('f');

        $result = $qb
            ->select('COUNT(f.id)')
            ->andWhere($qb->expr()->eq('f.user', ':userId'))
            ->andWhere($qb->expr()->eq('f.book', ':bookId'))
            ->setParameter('userId', $userId)
            ->setParameter('bookId', $bookId)
            ->getQuery()
            ->getSingleScalarResult();

        return $result > 0;
    }
}
