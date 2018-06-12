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


	/**
	 * Formulaire qui permet de modifier une question ainsu que les réponses associées, permet aussi d'ajouter des réponses
	 * @Route("profile/quizz/{id}/question/{questionId}/edit/", name="editQuestion", requirements={"id"="\d+", "questionId"="\d+"})
	 */
	public function editQuestion($id, $questionId, Request $request, ImageService $imageService, SecurityChecker $securityChecker)
	{
	    //__ On vérifie que les variable récupéré en get soient cohérentes (question qui correspond au quizz etc..)
	    try {

	        $question = $securityChecker->getCheckedQuestion($id, $questionId);

	    } catch (\Exception $e) {

	        return $this->redirectToRoute('userQuizz');
	    }

	    //__ On récupère l'image associé à la question
	    $image_question = $question->getImage();

	    //__ On réinitialise l'image pour générer un formulaire
	    $image = new Image();
	    $question->setImage($image);

	    //__ On récupère les images associées aux réponses
	    $images_answers = [];

	    foreach ($question->getAnswers() as $answer) {

	        $images_answers[$answer->getId()] = $answer->getImage();

	    }

	    //__ On hydrate nos entités avec les données récupéré en base de données, sauf pour les entitées images
	    $form = $this->createForm(QuestionType::class, $question);

        //__ On vérifie le formulaire
	    $form->handleRequest($request);

	    //__ Si le formulaire est soumis et qu'il est valide on enregistre nos entités en base de données
	    if ($form->isSubmitted() && $form->isValid())
	    {

	        $entityManager = $this->getDoctrine()->getManager();

	        //__ Si l'utilisateur associe une nouvelle image à la question, on remplace l'ancienne par la nouvelle
	        if($question->getImage()->getFile() != null) {

	            //__ On suprrime l'ancienne image de la question si elle existe
	            if($image_question !== null) {

	                $imageService->removeImage($image_question, Constant::PATH_IMAGE_QUESTION);
	            }

	            //__ On upload la nouvelle image
	            $file = $question->getImage()->getFile();
	            $fileName = $imageService->upload($file, 'question');

	            if (!$fileName){
	                throw $this->createNotFoundException(
	                    'Ce dossier d\'image n\'est pas autorisé ou n\'existe pas'
	                    );
	            }
	            //__ On met à jour les données de la question
	            $question->getImage()->setUrl($fileName);
	            $question->getImage()->setUpdated(new \DateTime());
	            $question->getImage()->setAlt('Illustration de la question "' . $question->getEntitled() . '" ');
	            $entityManager->persist($question->getImage());

	        } else {
	            //__ Si l'utilisateur n'a pas joint de nouvelle image, on remet l'ancienne image
	            $question->setImage($image_question);
	        }

	        foreach($question->getAnswers() as $answer) {

	            //__ Si c'est une nouvelle réponse (qui n'exsiatait pas en base de données)
	            if($answer->getId() === null) {

                    //__ On crée les données de la nouvelle réponse réponse
	                $answer->setCreated(new \DateTime());
	                $answer->setUpdated(new \DateTime());
	                $answer->setQuestion($question);

	                //__ Si la réponse est accompagné d'une image, on l'upload sur le serveur et on met à jour la base de données
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

                //__ Sinon, il s'agit d'une réponse qui existe déjà en base de données et il faut juste mettre les information à jour
	            } else {

	                //__ Si l'utilisateur associe une nouvelle image à la reponse, , on remplace l'ancienne par la nouvelle
	                if($answer->getImage()->getFile() != null) {

	                    //__ Si il y avait une ancienne image associé à cette réponse on la surpprime
	                    if($images_answers[$answer->getId()] !== null) {

	                        $imageService->removeImage($images_answers[$answer->getId()], Constant::PATH_IMAGE_ANSWER);
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
	                    $answer->setImage($images_answers[$answer->getId()]);
	                }
	            }

	            $entityManager->persist($answer);

	        }

	        $entityManager->persist($question);
	        $entityManager->flush();

	        return $this->redirectToRoute('editQuizz', array('id' => $id));
	    }

		$constant = new Constant();

	    //__ View
	    return $this->render(
	        'question/index.html.twig',
	        array(
	            'form' => $form->createView(),
		        'image' => $image_question,
	            'constant' => $constant	        )
	    );
	}


}
