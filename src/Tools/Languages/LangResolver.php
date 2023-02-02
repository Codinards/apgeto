<?php

namespace App\Tools\Languages;

use App\Tools\AppConstants;

class LangResolver
{
    protected static $languages = [
        'fr' => 'fr_FR',
        'en' => 'en_US',
        'es' => 'es_ES',
        'it' => 'it_IT',
        'pt' => 'pt_PT'
    ];

    protected static $keys = [
        'fr_FR' => 'fr',
        'en_US' => 'en',
        'es_ES' => 'es',
        'it_IT' => 'it',
        'pt_PT' => 'pt'
    ];

    static $languages_name = [
        'en' => 'English',
        'es' => 'Español',
        'fr' => 'Francais',
        /*'it' => 'Italiano',
        'pt' => 'portuges'*/
    ];

    public static function keyCorrespondance(?string $key = null)
    {
        $key = $key ?? AppConstants::$DEFAULT_LANGUAGE_KEY;
        return self::$languages[$key] ?? self::$languages[AppConstants::$DEFAULT_LANGUAGE_KEY];
    }

    public static function languageKey(?string $key = null)
    {
        $key = $key ?? AppConstants::$DEFAULT_LANGUAGE_ABBR;
        return self::$keys[$key] ?? self::$keys[AppConstants::$DEFAULT_LANGUAGE_ABBR];
    }

    public static function getLanguage(string $key): string
    {
        return in_array($key, self::$keys) ? self::$languages[$key] : (in_array($key, self::$languages) ? $key : self::keyCorrespondance());
    }

    public static function getkey(string $language)
    {
        return in_array($language, self::$languages) ? self::$keys[$language] : (in_array($language, self::$keys) ? $language : self::languageKey());
    }

    public static function getLanguages(): array
    {
        return self::$languages;
    }

    public static function implodeLanguage(string $char = ' '): string
    {
        return implode($char, self::$languages);
    }
}
