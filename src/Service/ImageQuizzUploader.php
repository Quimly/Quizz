<?php
// src/Service/ImageQuizzUploader.php
namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageQuizzUploader
{
    private $quizzDirectory;
    
    public function __construct($quizzDirectory)
    {
        $this->$quizzDirectory = $quizzDirectory;
    }
    
    public function upload(UploadedFile $file)
    {
        $fileName = md5(uniqid()).'.'.$file->guessExtension();
        
        $file->move($this->getTargetDirectory(), $fileName);
        
        return $fileName;
    }
    
    public function getTargetDirectory()
    {
        return $this->quizzDirectory;
    }
}