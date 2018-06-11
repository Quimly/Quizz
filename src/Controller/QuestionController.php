<?php

namespace App\Controller;

use App\Service\ImageService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use App\Service\SecurityChecker;
use App\Form\QuestionType;
use App\Entity\Question;
use App\Entity\Image;
use App\Entity\Answer;
use App\Entity\Constant;

/**
 *
 * Gestion des questions
 * @author arnaud et rémy
 *
 */
class QuestionController extends Controller
{
	/**
	 * Crée une question qui sera lié à un quizz. Possibilité d'associé une image a cette question et de l'uploader sur le serveur
	 * @param int  $id Id du quizz associé à la question
	 * @param Request  $request
	 * @param ImageService $imageService
	 * @Route("profile/quizz/{id}/addquestion/", name="addQuestion", requirements={"id"="\d+"})
	 */
    public function createQuestion($id, Request $request, ImageService $imageService, SecurityChecker $securityChecker)
	{
        try {

		  $quizz = $securityChecker->getCheckedQuizz($id);

        } catch (\Exception $e) {

            return $this->redirectToRoute('userQuizz');
        }

        //__ Initialisation des instances question et image
		$question = new Question();
		$image = new Image();
		$question->setImage($image);

		for($i = 0; $i < 2; $i++){
		    ${'answer_'.$i} = new Answer();
		    ${'image_'.$i} = new Image();
		    ${'answer_'.$i}->setImage(${'image_'.$i});
		    $question->getAnswers()->add(${'answer_'.$i});
		}

		//__ Création du formulaire
		$form = $this->createForm(QuestionType::class, $question);

		//__ On hydrate nos entités avec les données du formulaire rempli par l'utilisateur
		$form->handleRequest($request);

		//__ Si le formulaire est soumis et qu'il est valide on enregistre nos entités en base de données
		if ($form->isSubmitted() && $form->isValid())
		{
			$question->setCreated(new \DateTime());
			$question->setUpdated(new \DateTime());
			$question->setQuizz($quizz);

			$entityManager = $this->getDoctrine()->getManager();

			//__ Si il y a une image associé à la question, on upload l'image dans le dossier public/img/question, sinon on associe la valeur null à l'attribut image
			if($question->getImage()->getFile() != null) {

				$file = $question->getImage()->getFile();
				$fileName = $imageService->upload($file, 'question');

				if (!$fileName){
					throw $this->createNotFoundException(
						'Ce dossier d\'image n\'est pas autorisé ou n\'existe pas'
					);
				}
				$question->getImage()->setUrl($fileName);
				$question->getImage()->setUpdated(new \DateTime());
				$question->getImage()->setAlt('Illustration de la question "' . $question->getEntitled() . '" ');
				$entityManager->persist($image);

			} else {
				$question->setImage(null);
			}

			foreach($question->getAnswers() as $answer) {

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

			    $entityManager->persist($answer);

			}

			$entityManager->persist($question);

			$entityManager->flush();

			return $this->redirectToRoute('editQuizz', array('id' => $id));
		}

		//__ View
		return $this->render(
			'question/index.html.twig',
			array('form' => $form->createView())
		);
	}

	/**
	 * Supprime une question et l'image associé à cette derniere du dossier img/question si elle existe
	 * @param int  $id Id du quizz associé à la question
	 * @param int  $id Id de la question a supprimer
	 * @Route("profile/quizz/remove/{id}/question/{questionId}/", name="removeQuestion", requirements={"id"="\d+", "questionId"="\d+"})
	 */
	public function removeQuestion($id, $questionId, ImageService $imageService, SecurityChecker $securityChecker)
	{

	    try {

	        $question = $securityChecker->getCheckedQuestion($id, $questionId);

	    } catch(\Exception $e) {

	        return $this->redirectToRoute('editQuizz', array('id' => $id));
	    }

	    $imageService->removeImagesQuestion($question);

		$entityManager = $this->getDoctrine()->getManager();
		$entityManager->remove($question);
		$entityManager->flush();

		return $this->redirectToRoute('editQuizz', array('id' => $id));
	}
}
