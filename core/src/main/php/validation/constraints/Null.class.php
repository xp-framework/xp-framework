<?php namespace validation\constraints;

use validation\ConstraintValidator;
use validation\Violation;

/**
 * Checks if a value is null
 *
 * Example:
 * ```
 * #[@Assert([
 * # array('type'=>'validation.constraints.Null')
 * #])]
 * ```
 */
class Null extends ConstraintValidator {

  const VIOLATION_TYPE_NULL= 'null';

  /**
   * Returns default options for this validator
   *
   * @return array
   */
  protected function getDefaultOptions() {
    return array(
      'message' => 'validation#null'
    );
  }

  /**
   * Returns true if the object is valid.
   *
   * @param $object
   * @return bool
   */
  public function validate($object) {
    if ($object !== null) {
      $this->context->addViolation(
        new Violation(
          self::VIOLATION_TYPE_NULL,
          $this->options['message']
        )
      );
      return false;
    }
    return true;
  }

}
