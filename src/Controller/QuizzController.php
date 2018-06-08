<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use App\Service\ImageUploader;
use App\Service\SecurityChecker;

use App\Form\QuizzType;

use App\Entity\Constant;
use App\Entity\Quizz;
use App\Entity\Image;

class QuizzController extends Controller
{
	/**
	 * @Route("profile/quizz/", name="createQuizz")
	 */
	public function createQuizz(Request $request, ImageUploader $imageUploader)
	{
		$quizz = new Quizz();
		$image = new Image();
		$quizz->setImage($image);

		$form = $this->createForm(QuizzType::class, $quizz);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$quizz->setUser($this->getUser());
			$quizz->setCreated(new \DateTime());
			$quizz->setUpdated(new \DateTime());

			$entityManager = $this->getDoctrine()->getManager();

			if($quizz->getImage()->getFile() != null) {

				$file = $quizz->getImage()->getFile();
				$fileName = $imageUploader->upload($file, 'quizz');

				if (!$fileName){
					throw $this->createNotFoundException(
						'Ce dossier d\'image n\'est pas autorisé ou n\'existe pas'
					);
				}
				$quizz->getImage()->setUrl($fileName);
				$quizz->getImage()->setUpdated(new \DateTime());
				$quizz->getImage()->setAlt('Illustration du quizz "' . $quizz->getTitle() . '" ');
				$entityManager->persist($image);

			} else {
				$quizz->setImage(null);
			}

			$entityManager->persist($quizz);

			$entityManager->flush();

			return $this->redirectToRoute('userQuizz');


		}

		return $this->render(
			'quizz/index.html.twig',
			array('form' => $form->createView())
		);
	}

	/**
	 * @Route("profile/quizz/{id}/", name="editQuizz", requirements={"id"="\d+"})
	 */
	public function editQuizz($id, SecurityChecker $securityChecker)
	{

	    $quizz = $securityChecker->getCheckedQuizz($id);

		$constant = new Constant();

		return $this->render(
			'quizz/edit.html.twig',[
			'quizz' => $quizz,
			'constant' => $constant
		]);
	}

	/**
	 * @Route("profile/quizz/remove/{id}", name="removeQuizz", requirements={"id"="\d+"})
	 */
	public function removeQuizz($id, FileSystem $fileSystem, SecurityChecker $securityChecker)
	{

	    $quizz = $securityChecker->getCheckedQuizz($id);

		if($quizz->getImage() != null) {

			$fileSystem->remove(Constant::PATH_IMAGE_QUIZZ . $quizz->getImage()->getUrl());
		}

		foreach ($quizz->getQuestions() as $question) {

		    if($question->getImage() != null) {

		        $fileSystem->remove(Constant::PATH_IMAGE_QUESTION . $question->getImage()->getUrl());
		    }

		    foreach ($question->getAnswers() as $answer) {

		        if($answer->getImage() != null) {

		            $fileSystem->remove(Constant::PATH_IMAGE_ANSWER . $answer->getImage()->getUrl());
		        }
		    }
		}

		$entityManager = $this->getDoctrine()->getManager();
		$entityManager->remove($quizz);
		$entityManager->flush();

		return $this->redirectToRoute('userQuizz');
	}
}