<?php

namespace App\Repository;

use App\Entity\Quizz;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Quizz|null find($id, $lockMode = null, $lockVersion = null)
 * @method Quizz|null findOneBy(array $criteria, array $orderBy = null)
 * @method Quizz[]    findAll()
 * @method Quizz[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuizzRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Quizz::class);
    }

    public function getFullQuizzById($id)
    {

	    $q = $this->createQueryBuilder('quizz')
		    // quizz.questions refers to the "questions" property on quizz
		          ->leftJoin('quizz.questions', 'q')
		    // selects all the questions  data to avoid the query
		          ->addSelect('q')
		    // q.answers refers to the "answers" property on question
		          ->leftJoin('q.answers', 'a')
		    // selects all the answers data to avoid the query
		          ->addSelect('a')
		    // selects the image data to quizz if it exist
		          ->leftJoin('quizz.image', 'quizz_i')
		    // selects all the quizz images  data to avoid the query
		          ->addSelect('quizz_i')
		    // selects the image data to questions if it exist
		          ->leftJoin('q.image', 'q_i')
		    // selects all the questions images  data to avoid the query
		          ->addSelect('q_i')
		    // selects the image data to answers if it exist
		          ->leftJoin('a.image', 'q_a')
		    // selects all the answers images  data to avoid the query
		          ->addSelect('q_a')
		    // Filter by Quizz Id
		          ->andWhere('quizz.id = :id')
		    // change placeholder by the value
		          ->setParameter('id', $id);

	    try {
		    return $q->getQuery()->getOneOrNullResult();
	    }
	    catch(\Doctrine\ORM\NonUniqueResultException $e) {
		    return null;
	    }

    }



//    /**
//     * @return Quizz[] Returns an array of Quizz objects
//     */
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
    public function findOneBySomeField($value): ?Quizz
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
