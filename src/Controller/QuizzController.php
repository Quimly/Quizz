<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use App\Service\SecurityChecker;
use App\Service\ImageService;
use App\Form\QuizzType;
use App\Entity\Constant;
use App\Entity\Quizz;
use App\Entity\Image;

class QuizzController extends Controller
{
	/**
	 * @Route("profile/quizz/", name="createQuizz")
	 */
	public function createQuizz(Request $request, ImageService $imageService)
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
				$fileName = $imageService->upload($file, 'quizz');

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
	    try{

	        $quizz = $securityChecker->getCheckedQuizz($id);

	    } catch(\Exception $e){

	        return $this->redirectToRoute('userQuizz');
	    }


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
	public function removeQuizz($id, ImageService $imageService, SecurityChecker $securityChecker)
	{

	    try{

	        $quizz = $securityChecker->getCheckedQuizz($id);

	    } catch(\Exception $e) {

	        return $this->redirectToRoute('userQuizz');
	    }

		$imageService->removeImage(Constant::PATH_IMAGE_QUIZZ, $quizz->getImage());

		foreach ($quizz->getQuestions() as $question){

			$imageService->removeImage(Constant::PATH_IMAGE_QUESTION, $question->getImage());

			foreach ($question->getAnswers() as $answer){

				$imageService->removeImage(Constant::PATH_IMAGE_ANSWER, $answer->getImage());
			}
		}

		$entityManager = $this->getDoctrine()->getManager();
		$entityManager->remove($quizz);
		$entityManager->flush();

		return $this->redirectToRoute('userQuizz');
	}
}