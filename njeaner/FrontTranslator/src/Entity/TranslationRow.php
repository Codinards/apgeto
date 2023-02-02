<?php

declare(strict_types=1);

namespace Njeaner\FrontTranslator\Entity;

class TranslationRow
{
    private int $index;
    /**
     * Identifier of row transduction in the file
     * 
     * @example "user.name" identifier for username
     *
     * @var null|string
     */
    private ?string $id;

    /**
     * Translation text for a $id identifier
     * 
     * @var null|string
     */
    private ?string $translation;


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
     * Get identifier of row transduction in the file
     *
     * @return  string
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * Set identifier of row transduction in the file
     *
     * @param  string  $id  Identifier of row transduction in the file
     *
     * @return  self
     */
    public function setId(?string $id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get translation text for a $id identifier
     *
     * @return  string
     */
    public function getTranslation(): ?string
    {
        return $this->translation;
    }

    /**
     * Set translation text for a $id identifier
     *
     * @param  string  $translation  Translation text for a $id identifier
     *
     * @return  self
     */
    public function setTranslation(?string $translation)
    {
        $this->translation = $translation;

        return $this;
    }

    /**
     * Get locale language for the translation message
     *
     * @return  string
     */
    public function getLocale(): ?string
    {
        return $this->locale;
    }

    /**
     * Set locale language for the translation message
     *
     * @param  string  $locale  Locale language for the translation message
     *
     * @return  self
     */
    public function setLocale(?string $locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Get domain translation
     */
    public function getDomain(): ?string
    {
        return $this->domain;
    }

    /**
     * Set domain translation
     *
     * @return  self
     */
    public function setDomain(?string $domain)
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * Get the value of index
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * Set the value of index
     *
     * @return  self
     */
    public function setIndex($index)
    {
        $this->index = $index;

        return $this;
    }
}
