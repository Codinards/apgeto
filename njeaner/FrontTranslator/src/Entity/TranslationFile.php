<?php

declare(strict_types=1);

namespace Njeaner\FrontTranslator\Entity;

class TranslationFile
{
    /**
     * All translation rows of a file
     * 
     * @var <TranslationFile>
     */
    private array $rows;

    /**
     * Locale language for the translation message
     * 
     * @var null|string
     */
    private ?string $locale;


    /**
     * Domain translation
     * 
     * @example "form" for form domain translation
     * 
     * @var null|string
     */
    private ?string $domain;

    /**
     * Extension type of translation file
     * 
     * @var null|string
     */
    private ?string $fileExtension;

    public function __construct(string $locale, string $domain, ?string $fileExtension = '.yaml')
    {
        if ($fileExtension and stripos($fileExtension, '.') !== 0) $fileExtension = '.' . $fileExtension;
        $this->locale = $locale;
        $this->domain = $domain;
        $this->fileExtension = $fileExtension;
    }

    /**
     * Get all translation rows of a file
     *
     * @return  TranslationRow[]
     */
    public function getRows(): array
    {
        return $this->rows;
    }

    /**
     * Set all translation rows of a file
     *
     * @param  <TranslationFile>  $rows  All translation rows of a file
     *
     * @return  self
     */
    public function setRows(array $rows)
    {
        $this->rows = $rows;

        return $this;
    }

    public function fillRows(array $rows): self
    {
        $i = 0;
        foreach ($rows as $id => $trans) {
            $this->addRow(
                (new TranslationRow)
                    ->setId($id)
                    ->setTranslation($trans)
                    ->setDomain($this->domain)
                    ->setLocale($this->locale)
                    ->setIndex($i)
            );
            $i++;
        }

        return $this;
    }

    public function addRow(TranslationRow $row): self
    {
        $this->rows[] = $row;

        return $this;
    }

    /**
     * Get locale language for the translation message
     *
     * @return  null|string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Set locale language for the translation message
     *
     * @param  null|string  $locale  Locale language for the translation message
     *
     * @return  self
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Get translation domain
     *
     * @return  null|string
     */
    public function getDomain(): ?string
    {
        return $this->domain;
    }

    /**
     * Set translation domain
     *
     * @param  string  $domain  Domain translation
     *
     * @return  self
     */
    public function setDomain(string $domain)
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * Get the value of fileExtension
     */
    public function getFileExtension()
    {
        return $this->fileExtension;
    }

    /**
     * Set the value of fileExtension
     *
     * @return  self
     */
    public function setFileExtension($fileExtension)
    {
        $this->fileExtension = $fileExtension;

        return $this;
    }


    public function getFileName(): string
    {
        return $this->domain . '.' . $this->locale . $this->fileExtension;
    }
}
