<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Filesystem\Filesystem;
use App\Service\ImageUploader;
use App\Form\QuestionType;
use App\Entity\Constant;
use App\Entity\Question;
use App\Entity\Quizz;
use App\Entity\Image;

class QuestionController extends Controller
{
	/**
	 * @Route("profile/quizz/{id}/addquestion/", name="addQuestion", requirements={"id"="\d+"})
	 */
	public function createQuestion($id, Request $request, ImageUploader $imageUploader)
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
		$image = new Image();
		$question->setImage($image);

		$form = $this->createForm(QuestionType::class, $question);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$question->setCreated(new \DateTime());
			$question->setUpdated(new \DateTime());
			$question->setQuizz($quizz);

			$entityManager = $this->getDoctrine()->getManager();

			if($question->getImage()->getFile() != null) {

				$file = $question->getImage()->getFile();
				$fileName = $imageUploader->upload($file, 'question');

				if (!$fileName){
					throw $this->createNotFoundException(
						'Ce dossier d\'image n\'est pas autorisé ou n\'existe pas'
					);
				}
				$question->getImage()->setUrl($fileName);
				$question->getImage()->setUpdated(new \DateTime());
				$entityManager->persist($image);

			} else {
				$question->setImage(null);
			}

			$entityManager->persist($question);

			$entityManager->flush();

			return $this->redirectToRoute('editQuizz', array('id' => $id));


		}

		return $this->render(
			'question/index.html.twig',
			array('form' => $form->createView())
		);
	}

	/**
	 * @Route("profile/quizz/remove/{id}/question/{questionId}/", name="removeQuestion", requirements={"id"="\d+", "questionId"="\d+"})
	 */
	public function removeQuestion($id, $questionId, QuizzController $quizzController)
	{

		$entityManager = $this->getDoctrine()->getManager();
		$question = $entityManager->getRepository(Question::class)->find($questionId);
		$quizz = $entityManager->getRepository(Quizz::class)->find($id);

//		$this->securityCheck($quizz, $id, $question, $questionId, $quizzController);
//		TODO: check error on "this->getUser()" from accessed from outside controller

		if (!$quizz) {
			throw $this->createNotFoundException(
				'No quizz found for id '.$id
			);
		} else if ($quizz->getUser() != $this->getUser()){
			throw $this->createNotFoundException(
				'This user doesn\'t own this quizz'
			);
		}

		if ($quizz && !$question) {
			throw $this->createNotFoundException(
				'No question found for id '.$questionId
			);
		} else if ($quizz && ($question->getQuizz() !== $quizz)){
			throw $this->createNotFoundException(
				'This question isn\'t part of this quizz'
			);
		}


		if($question->getImage() != null) {

			$fileSystem = new Filesystem();
			$fileSystem->remove(Constant::PATH_IMAGE_QUESTION . $question->getImage()->getUrl());
		}

		$entityManager->remove($question);
		$entityManager->flush();

		return $this->redirectToRoute('editQuizz', array('id' => $id));
	}

	public function securityCheck(Quizz $quizz, $id, Question $question, $questionId, QuizzController $quizzController)
	{
		$quizzController->securityCheck($id, $quizz);

		if (!$question) {
			throw $this->createNotFoundException(
				'No question found for id '.$questionId
			);
		} else if ($question->getQuizz() !== $quizz){
			throw $this->createNotFoundException(
				'This question isn\'t part of this quizz'
			);
		}

	}
}
