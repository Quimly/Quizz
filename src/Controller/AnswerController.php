<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use App\Form\addAnswerType;

use App\Service\SecurityChecker;
use App\Service\ImageService;
use App\Entity\Answer;
use App\Entity\Image;
use App\Entity\Constant;

class AnswerController extends Controller
{
    /**
     * @Route("profile/quizz/{id}/question/{questionId}/addAnswer/", name="addAnswer", requirements={"id"="\d+", "questionId"="\d+"})
     */
    public function createAnswer($id, $questionId, Request $request, ImageService $imageService, SecurityChecker $securityChecker)
    {
        try {

            $question = $securityChecker->getCheckedQuestion($id, $questionId);

        } catch (\Exception $e) {

            return $this->redirectToRoute('userQuizz');
        }

        $images = [];

        foreach ($question->getAnswers() as $answer) {

            $images[$answer->getId()] = $answer->getImage();

        }

        //__ Création du formulaire
        $form = $this->createForm(addAnswerType::class, $question);

        //__ On hydrate nos entités avec les données du formulaire rempli par l'utilisateur
        $form->handleRequest($request);

        //__ Si le formulaire est soumis et qu'il est valide on enregistre nos entités en base de données
        if ($form->isSubmitted() && $form->isValid())
        {

           $entityManager = $this->getDoctrine()->getManager();

            foreach($question->getAnswers() as $answer) {

                if($answer->getId() === null) {

                    $answer->setCreated(new \DateTime());
                    $answer->setUpdated(new \DateTime());
                    $answer->setQuestion($question);

                    if($answer->getImage()->getFile() != null) {

                        $file = $answer->getImage()->getFile();
                        $fileName = $imageService->upload($file, 'answer');

                        if (!$fileName){
                            throw $this->createNotFoundException(
                                'Ce dossier d\'image n\'est pas autorisé ou n\'existe pas'
                                );
                        }
                        $answer->getImage()->setUrl($fileName);
                        $answer->getImage()->setUpdated(new \DateTime());
                        $answer->getImage()->setAlt('Illustration de la réponse "' . $answer->getEntitled() . '" ');
                        $entityManager->persist($answer->getImage());

                    } else {
                        $answer->setImage(null);
                    }

                } else {

                    if($answer->getImage()->getFile() != null) {

                        if($images[$answer->getId()] !== null) {

                            $imageService->removeImage($images[$answer->getId()], Constant::PATH_IMAGE_ANSWER);
                        }

                        $file = $answer->getImage()->getFile();
                        $fileName = $imageService->upload($file, 'answer');

                        if (!$fileName){
                            throw $this->createNotFoundException(
                                'Ce dossier d\'image n\'est pas autorisé ou n\'existe pas'
                                );
                        }
                        $answer->getImage()->setUrl($fileName);
                        $answer->getImage()->setUpdated(new \DateTime());
                        $answer->getImage()->setAlt('Illustration de la réponse "' . $answer->getEntitled() . '" ');
                        $entityManager->persist($answer->getImage());

                    } else {
                        $answer->setImage($images[$answer->getId()]);
                    }
                }

                $entityManager->persist($answer);

            }

            $entityManager->persist($question);
            $entityManager->flush();

            return $this->redirectToRoute('editQuizz', array('id' => $id));
        }

        //__ View
        return $this->render(
            'answer/addAnswer.html.twig',
            array(
                'question' => $question,
                'form' => $form->createView()
            )
        );
    }


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

    private function uploadImage($file) {

    }
}
