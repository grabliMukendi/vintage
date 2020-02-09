<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;


class AvatarUploader 
{
    private $targetDirectory;

    public function __construct($targetDirectory) 
    {
        $this->targetDirectory = $targetDirectory;
    }
    
    public function uploadFile(UploadedFile $file) : string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
        $file_name = md5(uniqid()) .'.'. $file->guessExtension();
        
        try 
        {
            $file->move($this->getTargetDir(), $file_name);
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }
        
        return $file_name;
    }
    
    private function getTargetDir() 
    {
        return $this->targetDirectory;
    }

    public function removeFile($file)
    {
        if($file) 
        {
            $file_path = $this->getTargetDir().'/'.$file;
            if(file_exists($file_path)) unlink($file_path);
        }
    }
}