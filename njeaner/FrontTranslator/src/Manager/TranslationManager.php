<?php

declare(strict_types=1);

namespace Njeaner\FrontTranslator\Manager;

use Exception;
use Njeaner\FrontTranslator\DependencyInjection\FrontTranslatorExtension;
use Njeaner\FrontTranslator\Entity\TranslationLocale;
use Njeaner\FrontTranslator\Entity\TranslationFile;
use Njeaner\FrontTranslator\Entity\TranslationRow;
use Symfony\Component\Translation\DataCollectorTranslator;
use Symfony\Component\Yaml\Yaml;
use Symfony\Contracts\Translation\TranslatorInterface;

class TranslationManager
{


    /**
     * @var DataCollectorTranslator
     */
    protected $translator;

    static $configs = [];

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }


    public function loadFile()
    {
        $languages = [];

        if (!empty(self::$configs)) {
            foreach (self::$configs['languages'] as $lang) {
                $langs = (new TranslationLocale)->setlocale($lang);
                foreach ($this->translator->getCatalogue($lang)->all() as $domain => $values) {
                    $langs->addFile(
                        (new TranslationFile($lang, $domain))->fillRows($values, $domain),
                        $domain
                    );
                }
                $languages[$lang] = $langs;
            }
        }
        return $languages;
    }


    public function find(string $id, string $domain, string $locale): ?TranslationRow
    {
        return $this->getLocaleFiles($locale)->find($id, $domain);
    }

    public function findIndex(int $index, string $domain, string $locale): ?TranslationRow
    {


        return $this->getLocaleFiles($locale)->find($index, $domain);
    }

    public function findByDomain(string $domain, string $locale)
    {
        return $this->getLocaleFiles($locale)->getDomainFile($domain);
    }

    private function getLocaleFiles($locale): TranslationLocale
    {
        return $this->loadFile()[$locale];
    }

    public function translationFileToArray(TranslationFile $transaltionFile): array
    {
        $data[$transaltionFile->getDomain()] = [];
        foreach ($transaltionFile->getRows() as $translationRow) {
            $data[$transaltionFile->getDomain()][$translationRow->getId()] = $translationRow->getTranslation();
        }
        return $data;
    }

    public function translationLocaleToArray(TranslationLocale $translationLocale): array
    {
        $data[$translationLocale->getLocale()] = [];
        foreach ($translationLocale->getFiles() as $transaltionFile) {
            $data[$translationLocale->getLocale()] = array_merge(
                $data[$translationLocale->getLocale()],
                $this->translationFileToArray($transaltionFile)
            );
        }

        return $data;
    }
}
