<?php

namespace App\Form\Extensions\Validations;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity as ConstraintsUniqueEntity;

/**
 * @Annotation
 */
class UniqueEntity extends ConstraintsUniqueEntity
{
    use TranslatorTrait;

    public $message = 'This value is already used.';

    // public function __construct(array $options = [])
    // {
    //     $this->message = $this->trans($options['message'] ?? $this->message);
    //     parent::__construct($options);
    // }
}
