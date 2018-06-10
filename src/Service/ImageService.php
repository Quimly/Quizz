<?php


namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Filesystem;
use App\Entity\Image;


class ImageService
{
	private $targetDirectory;
	private $fileSystem;


	public function __construct($targetDirectory, Filesystem $filesystem)
	{
		$this->targetDirectory = $targetDirectory;
		$this->fileSystem = $filesystem;

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


	public function removeImage($folder, Image $image)
	{
		if ($image->getUrl() != null)
		{
			$this->fileSystem->remove($folder . '/' . $image->getUrl());
		}
	}
}