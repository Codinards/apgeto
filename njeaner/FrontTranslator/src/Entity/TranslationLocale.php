<?php

declare(strict_types=1);

namespace Njeaner\FrontTranslator\Entity;

use Njeaner\FrontTranslator\Entity\TranslationFile;

class TranslationLocale
{
    private  array $files = [];

    private array $domains = [];

    private string $locale;


    /**
     * Get the value of files
     * 
     * @return TranslationFile[]
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    /**
     * Set the value of files
     *
     * @return  self
     */
    public function setFiles(array $files): self
    {
        $this->files = $files;

        return $this;
    }

    public function addFile(TranslationFile $files, $domain): self
    {
        $this->addDomain($domain);
        $this->files[$domain] = $files;

        return $this;
    }

    /**
     * Get the value of domains
     */
    public function getDomains(): array
    {
        return $this->domains;
    }

    /**
     * Set the value of domains
     *
     * @return  self
     */
    public function setDomains($domains): self
    {
        $this->domains = $domains;

        return $this;
    }

    public function addDomain($domain): self
    {
        $this->domains[] = $domain;

        return $this;
    }

    /**
     * Get the value of locales
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * Set the value of locales
     *
     * @return  self
     */
    public function setLocale($locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    public function find($id, $domain): ?TranslationRow
    {
        /** @var TranslationFile $translations */
        $translations = $this->files[$domain];
        $rows = $translations->getRows();
        if (!empty($rows)) {
            foreach ($rows as $row) {
                if ($row->getId() == $id) return $row;
            }
        }
        return null;
    }

    public function findByIndex($index, $domain): ?TranslationRow
    {
        /** @var TranslationFile $translations */
        $translations = $this->files[$domain];
        return $translations->getRows()[$index] ?? null;
    }

    public function getDomainFile($domain): ?TranslationFile
    {
        return $this->files[$domain] ?? null;
    }
}
