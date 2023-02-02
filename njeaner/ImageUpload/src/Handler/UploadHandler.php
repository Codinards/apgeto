<?php

namespace Njeaner\ImageUpload\Handler;

use App\Tools\Request\UploadedFile;
use Njeaner\ImageUpload\File;
use Symfony\Component\PropertyAccess\PropertyAccess;

class UploadHandler
{
    protected $accessor;

    private $appDir;

    public function __construct()
    {
        $this->accessor = PropertyAccess::createPropertyAccessor();
    }

    public function uploadFile($entity, $annotation, $file)
    {
        $filename = uniqid(str_replace('.' . $file->getClientOriginalExtension(), '', $file->getClientOriginalName())) . "." . $file->getClientOriginalExtension();
        $file->move($annotation->getPath(), $filename);
        $this->accessor->setValue($entity, $annotation->getFilename(), $filename);
    }

    public function loadFileFromDatabase($entity, $property, $annotation)
    {
        $filename = $this->getFileFromFilename($entity, $annotation);
        if ($filename !== null) {
            $this->accessor->setValue($entity, $property, $filename);
        }
    }

    public function removeFileOld($entity, $annotation)
    {
        $filename = $this->getFileFromFilename($entity, $annotation);
        if ($filename !== null) {
            unlink($filename->getRealPath());
        }
    }

    private function getFileFromFilename($entity, $annotation)
    {
        $filename = $this->accessor->getValue($entity, $annotation->getFilename());
        if ($filename) {
            return new File($annotation->getPath() . DIRECTORY_SEPARATOR . $filename, false);
        }
        return null;
    }
}
