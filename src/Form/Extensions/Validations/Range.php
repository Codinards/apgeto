<?php

namespace App\Form\Extensions\Validations;

use Symfony\Component\Validator\Constraints\Range as ConstraintsRange;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\LogicException;
use Symfony\Component\Validator\Exception\MissingOptionsException;

/**
 * @Annotation
 */
class Range extends ConstraintsRange
{
    use TranslatorTrait;

    public $notInRangeMessage = 'This value should be between {{ min }} and {{ max }}.';
    public $minMessage = 'This value should be {{ limit }} or more.';
    public $maxMessage = 'This value should be {{ limit }} or less.';
    public $invalidMessage = 'This value should be a valid number.';

    public function __construct($options = null, array $params = [])
    {
        if (!isset($options['minMessage'])) {
            $this->minMessage = $this->trans($this->minMessage, $params);
        }
        if (!isset($options['maxMessage'])) {
            $this->maxMessage = $this->trans($this->maxMessage, $params);
        }
        if (!isset($options['notInRangeMessage'])) {
            $this->notInRangeMessage = $this->trans($this->notInRangeMessage, $params);
        }
        if (!isset($options['invalidMessage'])) {
            $this->invalidMessage = $this->trans($this->invalidMessage, $params);
        }
        if (\is_array($options)) {
            if (isset($options['min']) && isset($options['minPropertyPath'])) {
                throw new ConstraintDefinitionException(sprintf('The "%s" constraint requires only one of the "min" or "minPropertyPath" options to be set, not both.', static::class));
            }

            if (isset($options['max']) && isset($options['maxPropertyPath'])) {
                throw new ConstraintDefinitionException(sprintf('The "%s" constraint requires only one of the "max" or "maxPropertyPath" options to be set, not both.', static::class));
            }

            if ((isset($options['minPropertyPath']) || isset($options['maxPropertyPath'])) && !class_exists(PropertyAccess::class)) {
                throw new LogicException(sprintf('The "%s" constraint requires the Symfony PropertyAccess component to use the "minPropertyPath" or "maxPropertyPath" option.', static::class));
            }

            if (isset($options['min']) && isset($options['max'])) {
                $this->deprecatedMinMessageSet = isset($options['minMessage']);
                $this->deprecatedMaxMessageSet = isset($options['maxMessage']);

                // BC layer, should throw a ConstraintDefinitionException in 6.0
                if ($this->deprecatedMinMessageSet || $this->deprecatedMaxMessageSet) {
                    trigger_deprecation('symfony/validator', '4.4', '"minMessage" and "maxMessage" are deprecated when the "min" and "max" options are both set. Use "notInRangeMessage" instead.');
                }
            }
        }

        parent::__construct($options);

        if (null === $this->min && null === $this->minPropertyPath && null === $this->max && null === $this->maxPropertyPath) {
            throw new MissingOptionsException(sprintf('Either option "min", "minPropertyPath", "max" or "maxPropertyPath" must be given for constraint "%s".', __CLASS__), ['min', 'minPropertyPath', 'max', 'maxPropertyPath']);
        }
    }
}
