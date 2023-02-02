<?php

namespace App\Form;

use App\Tools\Twig\LocalLanguages;
use App\Twig\TranslatorTwigExtension;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Component\Form\AbstractType;
use Illuminate\Support\Collection;

class AppAbstractType extends AbstractType
{
    protected $appTranslator;

    protected $languageProvider;

    public function __construct(TranslatorTwigExtension $appTranslator, LocalLanguages $languageProvider)
    {
        $this->appTranslator = $appTranslator;
        $this->languageProvider = $languageProvider;
    }

    public function optionsMerge(string $child, array $options = []): array
    {
        if (isset($options['label_attr'])) {
            $options['label_attr']['style'] = isset($options['label_attr']['style'])
                ? $options['label_attr']['style'] . 'font-weight:bold;' : 'font-weight:bold;';
        } else {
            $options['label_attr']['style'] = 'font-weight: bold;';
        }
        $options['label'] = $options['label'] ?? $this->trans($child);
        $options['translation_domain'] = 'forms';
        return $options;
    }

    protected function trans(string $key, array $params = [], ?string $domain = null, ?string $local = null): string
    {
        return TranslatorTwigExtension::getInstance()
            ->__u($key, $params, $domain, $local ?? LocalLanguages::getInstance()->getLocaleKey());
    }

    protected function minMessage(array $params = [])
    {
        return $this->trans('value.equal.or.more.%limit%', $params);
    }

    protected function maxMessage(array $params = [])
    {
        return $this->trans('value.equal.or.less.%limit%', $params);
    }

    protected function collection(array $data = []):Collection
    {
        return new Collection($data);
    }

}
