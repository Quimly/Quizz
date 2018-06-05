<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Filesystem\Filesystem;
use App\Entity\Constant;
use App\Entity\Question;
use App\Entity\Quizz;
use App\Entity\Image;

class QuestionController extends Controller
{
    /**
     * @Route("profile/quizz/{id}/addquestion/", name="addQuestion", requirements={"id"="\d+"})
     */
    public function createQuizz($id, Request $request)
    {   
        $entityManager = $this->getDoctrine()->getManager();
        $quizz = $entityManager->getRepository(Quizz::class)->find($id);
        
        if (!$quizz) {
            throw $this->createNotFoundException(
                'No quizz found for id '.$id
                );
        } else if ($quizz->getUser() != $this->getUser()){
            throw $this->createNotFoundException(
                'This user doesn\'t own this quizz'
                );
        }
        
        $question = new Question();
        
        return $this->render(
            'question/index.html.twig'
            );
    }
}
