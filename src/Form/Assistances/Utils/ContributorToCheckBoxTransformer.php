<?php

namespace App\Form\Assistances\Utils;

use Symfony\Component\Form\DataTransformerInterface;

class ContributorToCheckBoxTransformer implements DataTransformerInterface
{

    public function transform($value)
    {
        if (null === $value) {
            return false;
        }

        return true;
    }

    public function reverseTransform($value)
    {
    }
}
