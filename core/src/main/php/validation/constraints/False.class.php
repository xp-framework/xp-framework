<?php namespace validation\constraints;

use validation\ConstraintValidator;
use validation\Violation;

/**
 * Checks if a value is false
 *
 * Example:
 * ```
 * #[@Assert([
 * # array('type'=>'validation.constraints.False')
 * #])]
 * ```
 */
class False extends ConstraintValidator {

  const VIOLATION_TYPE_FALSE= 'false';

  /**
   * Returns default options for this validator
   *
   * @return array
   */
  protected function getDefaultOptions() {
    return array(
      'message' => 'validation#false'
    );
  }

  /**
   * Returns true if the object is valid.
   *
   * @param $object
   * @return bool
   */
  public function validate($object) {
    if ($object !== false) {
      $this->context->addViolation(
        new Violation(
          self::VIOLATION_TYPE_FALSE,
          $this->options['message']
        )
      );
      return false;
    }
    return true;
  }

}
