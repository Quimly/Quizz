<?php


namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Filesystem;
use App\Entity\Image;
use App\Entity\Constant;
use App\Entity\Answer;
use App\Entity\Question;
use App\Entity\Quizz;


class ImageService
{
	private $targetDirectory;

	private $fileSystem;


	public function __construct($targetDirectory, Filesystem $filesystem)
	{
		$this->targetDirectory = $targetDirectory;
		$this->fileSystem = $filesystem;

	}

	public function getTargetDirectory()
	{
	    return $this->targetDirectory;
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

	public function removeImagesQuizz(Quizz $quizz)
	{
	    if ($quizz->getImage() != null)
	    {
	        $this->removeImage( $quizz->getImage() , Constant::PATH_IMAGE_QUIZZ);
	    }

	    foreach ( $quizz->getQuestions() as $question )
	    {
	        $this->removeImagesQuestion($question);
	    }
	}

	public function removeImagesQuestion(Question $question)
	{
	    if ($question->getImage() != null)
	    {
	        $this->removeImage( $question->getImage() , Constant::PATH_IMAGE_QUESTION);
	    }

	    foreach ( $question->getAnswers() as $answer )
	    {
	       $this->removeImagesAnswer($answer);
	    }
	}

	public function removeImagesAnswer(Answer $answer)
	{

	    if ($answer->getImage() != null)
	    {
	        $this->removeImage( $answer->getImage() ,Constant::PATH_IMAGE_ANSWER);
	    }
	}

	public function removeImage(Image $image, $folder)
	{
		if ($image->getUrl() != null)
		{
			$this->fileSystem->remove($folder . $image->getUrl());
		}
	}
}