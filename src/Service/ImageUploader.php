<?php
// src/Service/ImageUploader.php
namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageUploader
{
    private $targetDirectory;
    
    public function __construct($targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }
    
    public function upload(UploadedFile $file, $folder)
    {
        $authorizedFolders = ['quizz', 'user', 'question', 'answer'];
        
        if(!in_array($folder, $authorizedFolders)) {
            throw $this->createNotFoundException(
                'Ce dossier n\'image n\'est pas autorisé ou n\'existe pas'
                );
        }
        
        $fileName = md5(uniqid()).'.'.$file->guessExtension();
        
        $file->move($this->getTargetDirectory() . $folder, $fileName);
        
        return $fileName;
    }
    
    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }
}