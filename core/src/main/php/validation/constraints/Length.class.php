<?php namespace validation\constraints;

use validation\ConstraintValidator;
use validation\Violation;

/**
 * Class Length
 *
 * @author jzinnau
 *
 */
class Length extends ConstraintValidator {

  const VIOLATION_TYPE_TOOLONG= 'length_toolow';
  const VIOLATION_TYPE_TOOSHORT= 'length_tooshort';

  protected function getDefaultOptions() {
    return array(
      'min'             => 0,
      'max'             => 100,
      'messageTooLong'  => 'validation#length.toolong',
      'messageTooShort' => 'validation#length.tooshort'
    );
  }

  public function validate($object) {
    if (mb_strlen($object, \XP::ENCODING) < $this->options['min']) {
      $this->context->addViolation(
        new Violation(
          self::VIOLATION_TYPE_TOOSHORT,
          $this->options['messageTooShort']
        )
      );
      return false;
    }
    if (mb_strlen($object, \XP::ENCODING) > $this->options['max']) {
      $this->context->addViolation(
        new Violation(
          self::VIOLATION_TYPE_TOOLONG,
          $this->options['messageTooLong']
        )
      );
      return false;
    }
    return true;
  }

}