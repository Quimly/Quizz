<?php
// src/Service/SecurityChecker.php
namespace App\Service;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

use App\Entity\Quizz;
use App\Entity\Question;

class SecurityChecker
{
    private $entityManager;

    private $user;

    public function __construct(ObjectManager $em, TokenStorageInterface $tokenStorage)
    {
        $this->entityManager = $em;

        $this->user = $tokenStorage->getToken()->getUser();
    }

    public function getCheckedQuizz($id)
    {
        $quizz = $this->entityManager->getRepository(Quizz::class)->find($id);

        if (!$quizz) {

            throw new NotFoundHttpException('Le quizz n°'. $id . ' n\'existe pas');

        } else if($quizz->getUser() != $this->user) {

            throw new NotFoundHttpException('Ce quizz n\'appartient pas à cet utilisateur');

        }

        return $quizz;
    }

    public function getCheckedQuestion($quizz_id, $question_id)
    {
        $quizz = $this->getCheckedQuizz($quizz_id);

        $question = $this->entityManager->getRepository(Question::class)->find($question_id);

        if (!$question) {

            throw new NotFoundHttpException('La question n°'. $question_id . ' n\'existe pas');

        } else if($question->getQuizz() != $quizz) {

            throw new NotFoundHttpException('Cette question n\'appartient pas à ce quizz');
        }

        return $question;
    }
}