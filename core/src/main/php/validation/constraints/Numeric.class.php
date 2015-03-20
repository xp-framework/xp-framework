<?php namespace validation\constraints;

use validation\ConstraintValidator;
use validation\Violation;

/**
 * Checks if a value is numeric
 *
 * Example:
 * ```
 * #[@Assert([
 * # array('type'=>'validation.constraints.Numeric')
 * #])]
 * ```
 */
class Numeric extends ConstraintValidator {

  const VIOLATION_TYPE_NUMERIC= 'numeric';

  /**
   * Returns default options for this validator
   *
   * @return array
   */
  protected function getDefaultOptions() {
    return array(
      'message' => 'validation#numeric'
    );
  }

  /**
   * Returns true if the object is valid.
   *
   * @param $object
   * @return bool
   */
  public function validate($object) {
    if (!is_numeric($object)) {
      $this->context->addViolation(
        new Violation(
          self::VIOLATION_TYPE_NUMERIC,
          $this->options['message']
        )
      );
      return false;
    }
    return true;
  }

}