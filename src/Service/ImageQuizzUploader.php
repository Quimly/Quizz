<?php
// src/Service/ImageQuizzUploader.php
namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageQuizzUploader
{
    private $targetDirectory;
    
    public function __construct($targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }
    
    public function upload(UploadedFile $file)
    {
        $fileName = md5(uniqid()).'.'.$file->guessExtension();
        
        $file->move($this->getTargetDirectory(), $fileName);
        
        return $fileName;
    }
    
    public function getTargetDirectory()
    {
        var_dump($this->targetDirectory);
        return $this->targetDirectory;
    }
}