<?php

namespace Njeaner\ImageUpload;

use App\Tools\Request\FileBag;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File as FileFile;

class File extends FileFile
{
    private ?string $uploadPath = null;
    private bool $addPrePath = true;
    public function __construct(string $path, bool $checkPath = true, bool $addPrePath = true)
    {
        $this->uploadPath = dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'public';
        parent::__construct($path, $checkPath);
        $this->addPrePath = $addPrePath;
    }

    public function getRealPath(): string|false
    {
        return ($this->addPrePath === true ? $this->uploadPath : '') . $this->getPathname();
    }

    public function getLinkTarget(): string|false
    {
        return $this->getRealPath();
    }

    /**
     * @return self
     */
    protected function getTargetFile(string $directory, string $name = null)
    {
        if (!is_dir($directory)) {
            if (false === @mkdir($directory, 0777, true) && !is_dir($directory)) {
                throw new FileException(sprintf('Unable to create the "%s" directory.', $directory));
            }
        } elseif (!is_writable($directory)) {
            throw new FileException(sprintf('Unable to write in the "%s" directory.', $directory));
        }

        $target = rtrim($directory, '/\\') . \DIRECTORY_SEPARATOR . (null === $name ? $this->getBasename() : $this->getName($name));

        return new self($target, false);
    }
}
