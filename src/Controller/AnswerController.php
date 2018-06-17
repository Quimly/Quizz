<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Constant;
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

    /**
     * @Route("profile/quizz/{id}/question/{questionId}/answer/{answerId}/removeImage/", name="removeAnswerImage", requirements={"id"="\d+", "questionId"="\d+", "answerId"="\d+"})
     */
    public function removeImageAjax($id, $questionId, $answerId, ImageService $imageService, SecurityChecker $securityChecker, Request $request) {

        $status = true;
        //__ On vérifie que les variable récupéré en get soient cohérentes (question qui correspond au quizz etc..)
        try {

            $answer = $securityChecker->getCheckedAnswer($id, $questionId, $answerId);

            $imageService->removeImage( $answer->getImage() ,Constant::PATH_IMAGE_ANSWER);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($answer->getImage());
            $answer->setImage(null);
            $entityManager->persist($answer);
            $entityManager->flush();

        } catch (\Exception $e) {

            $status = false;
        }
        $button = $request->request->get('idButton');
        return $this->json(array('status' => $status, 'idButton' => $button));
    }
}
