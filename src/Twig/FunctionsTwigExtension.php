<?php

namespace App\Twig;

use Mpdf\Tag\B;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class FunctionsTwigExtension extends AbstractExtension
{

    public function getFilters(): array
    {
        return [];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('noEmpty', [$this, 'noEmpty']),
            new TwigFunction('reduce_data', [$this, 'reduceData']),
            new TwigFunction('empty', 'empty'),
            new TwigFunction('in_array', 'in_array'),
            new TwigFunction('is_array', 'is_array'),
            new TwigFunction('json_encode', 'json_encode'),
            new TwigFunction('not_in_array', [$this, 'not_in_array']),
            new TwigFunction('count', [$this, 'count']),
            new TwigFunction('merge', 'array_merge'),
            new TwigFunction('isset', [$this, 'isset']),
            new TwigFunction('php_isset',  'isset'),
            new TwigFunction('_and', [$this, 'and']),
            new TwigFunction('_or', [$this, 'or']),
            new TwigFunction('is_true', [$this, 'isTrue']),
            new TwigFunction('is_false', [$this, 'isFalse']),
            new TwigFunction('is_null', [$this, 'isNull']),
            new TwigFunction('print', [$this, 'print']),
            new TwigFunction('int', [$this, 'int']),
            new TwigFunction('float', [$this, 'float']),
            new TwigFunction('stripos', 'stripos')
        ];
    }

    public function int($value): int
    {
        return (int) $value;
    }

    public function float($value): float
    {
        return (float) $value;
    }

    public function print($data)
    {
        echo $data;
    }

    public function isset($value)
    {
        return isset($value);
    }

    public function not_in_array($value, array $array)
    {
        return !in_array($value, $array);
    }

    public function noEmpty($value): bool
    {
        return !empty($value);
    }

    public function count(array $data)
    {
        return count($data);
    }

    public function reduceData($data, string $fn)
    {
        if (!is_array($data)) $data = $data->toArray();
        return $this->$fn($data);
    }

    private function tontinedata($data)
    {

        $json =  '{' . \array_reduce(
            $data,
            fn ($old, $new) => $old . '"' . $new->getTontineur()->getName() . '":{'
                . '"user": "' . $new->getTontineur()->getName() . '", "id": "' . $new->getTontineur()->getId() . '", "count": "' . ($new->getCount() - $new->getCountDemiNom()) . '", "demi": "' . $new->getCountDemiNom() . '"'
                . "},",
            ''
        );
        return substr($json, 0, -1) . '}';
    }

    public function and(...$values)
    {
        $cond = true;
        foreach ($values as $value) {
            $cond = (bool) $value * $cond;
        }
        return $cond;
    }

    public function or(...$values)
    {
        foreach ($values as $value) {
            if ((bool) $value === true) {
                return true;
            }
        }
        return false;
    }

    public function isTrue($condition): bool
    {
        return (bool) $condition === true;
    }

    public function isFalse($condition): bool
    {
        return !$this->isTrue($condition);
    }

    public function isNull($condition): bool
    {
        return $condition === null;
    }
}
