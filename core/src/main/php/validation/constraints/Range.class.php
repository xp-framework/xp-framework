<?php namespace validation\constraints;

use validation\ConstraintValidator;
use validation\Violation;

/**
 * Class Range
 *
 * @author jzinnau
 *
 */
class Range extends ConstraintValidator {

  const VIOLATION_TYPE_TOOLOW= 'range_toolow';
  const VIOLATION_TYPE_TOOHIGH= 'range_toohigh';

  protected function getDefaultOptions() {
    return array(
      'min'             => 0,
      'max'             => 100,
      'messageTooLow'   => 'validation#length.toolow',
      'messageTooHigh'  => 'validation#length.toohigh'
    );
  }

  public function validate($object) {
    if ($this->options['min'] != null && $object < $this->options['min']) {
      $this->context->addViolation(
        new Violation(
          self::VIOLATION_TYPE_TOOLOW,
          $this->options['messageTooLow']
        )
      );
      return false;
    }
    if ($this->options['max'] != null && $object > $this->options['max']) {
      $this->context->addViolation(
        new Violation(
          self::VIOLATION_TYPE_TOOHIGH,
          $this->options['messageTooHigh']
        )
      );
      return false;
    }
    return true;
  }

}