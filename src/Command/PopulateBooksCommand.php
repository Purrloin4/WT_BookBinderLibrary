<?php

namespace App\Command;

use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Exception;
use League\Csv\Reader;
use League\Csv\Statement;
use League\Csv\UnavailableStream;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'populateBooks',
    description: 'Adds data to the books table',
)]
class PopulateBooksCommand extends Command
{
    private EntityManagerInterface $em;

    /**
     * CsvImportCommand constructor.
     *
     * @throws LogicException
     */
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();

        $this->em = $em;
    }

    /**
     * Configure.
     *
     * @throws InvalidArgumentException
     */
    protected function configure()
    {
        $this
            ->setName('csv:importBooks')
            ->setDescription('Imports the books CSV.')
        ;
    }

    /**
     * @throws UnavailableStream
     * @throws Exception
     * @throws \Doctrine\DBAL\Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Attempting import of Feed...');

        $reader = Reader::createFromPath('src/Data/books.csv');

        $reader->setHeaderOffset(0);
        $reader->setDelimiter(';');

        $stmt = Statement::create();

        $results = $stmt->process($reader);

        $io->text('Clearing table Book...');

        $querry = $this->em->getRepository(Book::class)->createQueryBuilder('c');
        $allBooks = $querry->delete()
            ->getQuery()
            ->execute()
        ;
        $connection = $this->em->getConnection();
        $connection->executeQuery('ALTER TABLE book AUTO_INCREMENT = 0');
        $this->em->flush();

        $io->text('Book cleared...');

        $io->progressStart(iterator_count($results));

        foreach ($results as $row) {
            $book = (new Book())
                ->setIsbn($row['isbn'])
                ->setAverageRating(floatval($row['average_rating']))
                ->setPublishedDate(new \DateTimeImmutable($row['publication_date']))
                ->setRatingsCount(intval($row['ratings_count']))
            ;

            $this->em->persist($book);

            $io->progressAdvance();
        }

        $this->em->flush();

        $io->progressFinish();
        $io->success('Command exited cleanly!');

        return 0;
    }
}
