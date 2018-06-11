<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use App\Service\SecurityChecker;
use App\Service\ImageService;

class AnswerController extends Controller
{

    /**
     *
     * @Route("profile/quizz/remove/{id}/question/{questionId}/answer/{answerId}/", name="removeAnswer", requirements={"id"="\d+", "questionId"="\d+", "answerId"="\d+"})
     */
    public function removeAnswer($id, $questionId, $answerId, ImageService $imageService, SecurityChecker $securityChecker)
    {

        try {

            $answer = $securityChecker->getCheckedAnswer($id, $questionId, $answerId);

        } catch(\Exception $e) {

            return $this->redirectToRoute('editQuizz', array('id' => $id));
        }

        $imageService->removeImagesAnswer($answer);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($answer);
        $entityManager->flush();

        return $this->redirectToRoute('editQuizz', array('id' => $id));
    }
}
