<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 *
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    public function save(Book $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Book $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Finds books by their title.
     *
     * @param string $value The title to search for
     * @return Book[] Returns an array of Book objects matching the given title
     */
    public function findByTitle(string $value): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.title = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Finds a book by its title.
     *
     * @param string $value The title to search for
     * @return Book|null Returns a Book object matching the given title, or null if not found
     */
    public function findOneByTitle(string $value): ?Book
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.title = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * Finds popular books based on average rating, ratings count, and recent publication date.
     *
     * @param int $limit The maximum number of popular books to retrieve
     * @return Book[] Returns an array of popular Book objects
     */
    public function findPopularBooks(int $limit): array
    {
        $recentDate = new \DateTime('-3 years');

        return $this->createQueryBuilder('b')
            ->orderBy('b.averageRating', 'DESC')
            ->addOrderBy('b.ratingsCount', 'DESC')
            ->andWhere('b.publishedDate >= :recentDate')
            ->andWhere('b.ratingsCount > :minRatingsCount')
            ->andWhere('b.averageRating > :minAverageRating')
            ->setParameter('recentDate', $recentDate)
            ->setParameter('minRatingsCount', 1000)
            ->setParameter('minAverageRating', 4.0)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }


    /**
     * Finds the top-rated books based on average rating.
     *
     * @param int $limit The maximum number of top-rated books to retrieve
     * @return Book[] Returns an array of top-rated Book objects
     */
    public function findTopRatedBooks(int $limit): array
    {
        return $this->createQueryBuilder('b')
            ->orderBy('b.averageRating', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Finds a random selection of books published this year.
     *
     * @param int $limit The maximum number of books to retrieve
     * @return Book[] Returns an array of random Book objects published this year
     */
    public function findRandomBooksPublishedThisYear(int $limit): array
    {
        $currentYear = (int) date('Y');
        $startDate = new \DateTime("{$currentYear}-01-01");
        $endDate = new \DateTime();

        return $this->createQueryBuilder('b')
            ->andWhere('b.publishedDate >= :startDate')
            ->andWhere('b.publishedDate <= :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }


}
