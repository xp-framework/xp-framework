<?php namespace validation\constraints;

use validation\ConstraintValidator;
use validation\Violation;

/**
 * Checks if a numeric value is in a specific range
 * The allowed range can be set by the 'min' and 'max' parameters.
 * If one parameter is set to null, this constraint will be ignored.
 *
 * Example:
 * ```
 * #[@Assert([
 * # array('type'=>'validation.constraints.Range')
 * #])]
 * ```
 */
class Range extends ConstraintValidator {

  const VIOLATION_TYPE_TOOLOW= 'range_toolow';
  const VIOLATION_TYPE_TOOHIGH= 'range_toohigh';

  /**
   * Returns default options for this validator
   *
   * @return array
   */
  protected function getDefaultOptions() {
    return array(
      'min'             => 0,
      'max'             => 100,
      'messageTooLow'   => 'validation#length.toolow',
      'messageTooHigh'  => 'validation#length.toohigh'
    );
  }

  /**
   * Returns true if the object is valid.
   *
   * @param $object
   * @return bool
   */
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