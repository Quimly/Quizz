<?php


namespace App\Service;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Filesystem;


class ImageService
{
	private $targetDirectory;
	private $entityManager;


	public function __construct($targetDirectory, ObjectManager $em)
	{
		$this->targetDirectory = $targetDirectory;
		$this->entityManager = $em;
	}

	public function upload(UploadedFile $file, $folder)
	{
		$authorizedFolders = ['quizz', 'user', 'question', 'answer'];

		if(!in_array($folder, $authorizedFolders)) {
			return false;
		}

		$fileName = md5(uniqid()).'.'.$file->guessExtension();

		$file->move($this->getTargetDirectory() . $folder, $fileName);

		return $fileName;
	}

	public function getTargetDirectory()
	{
		return $this->targetDirectory;
	}


	/**
	 * @param $imageEntity
	 * $imageEntity = entity of class : Quizz | Question | Answer
	 */
	public function removeImages($imageEntity)
	{

		$class = explode('\\', get_class($imageEntity));  //__On récupère le nom de la classe contenant l'image

		$capsClass = strtoupper($class[2]); //__et on le passe en maj.

		switch ($capsClass) //__Selon le contexte on retire des fichiers images avec ou sans boucle.
		{
			case 'QUIZZ' :
				$this->remove($imageEntity, $capsClass);

				foreach ($imageEntity->getQuestions() as $question){
					$this->remove($question, 'QUESTION');

					foreach ($question->getAnswers() as $answer){
						$this->remove($answer, 'ANSWER');
					}
				}
				break;

			case 'QUESTION' :
				$this->remove($imageEntity, $capsClass);

				foreach ($imageEntity->getAnswers() as $answer){
					$this->remove($answer, 'ANSWER');
				}
				break;

			case 'ANSWER' :
				$this->remove($imageEntity, $capsClass);
		}
	}

	private function remove($imageEntity, $capsClass)
	{
		$folder = 'PATH_IMAGE_' . $capsClass; //__On concatène le nom constante du fichier de l'image.

		$fileSystem = new Filesystem();

		if( $imageEntity->getImage() != null) {

			//__On supprime le fichier ciblé (appel des constantes de fichier image + url de l'image)
			$fileSystem->remove(\constant('App\Entity\Constant::' . $folder) . '/' . $imageEntity->getImage()->getUrl());
		}
	}
}