<?php

declare(strict_types=1);

namespace Njeaner\ImageUpload\Annotations;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
class UploadableField
{
    /**
     * @var string
     */
    private string  $filename;

    /**
     * @var string
     */
    private string  $path;

    public function __construct(array $options)
    {
        if (empty($options['filename'])) {
            throw new \InvalidArgumentException("attribute filename missing in UploadableField");
        }
        if (empty($options['path'])) {
            throw new \InvalidArgumentException("attribute path missing in UploadableField");
        }
        $this->filename = $options['filename'];
        $this->path = str_replace('/', DIRECTORY_SEPARATOR, $options['path']);
    }


    /**
     * Get the value of filename
     *
     * @return  string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Get the value of path
     *
     * @return  string
     */
    public function getPath()
    {
        return $this->path;
    }
}
