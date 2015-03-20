<?php namespace validation\constraints;

use validation\ConstraintValidator;
use validation\Violation;

/**
 * Checks if a value is true
 *
 * Example:
 * ```
 * #[@Assert([
 * # array('type'=>'validation.constraints.True')
 * #])]
 * ```
 */
class True extends ConstraintValidator {

  const VIOLATION_TYPE_TRUE= 'true';

  /**
   * Returns default options for this validator
   *
   * @return array
   */
  protected function getDefaultOptions() {
    return array(
      'message' => 'validation#true'
    );
  }

  /**
   * Returns true if the object is valid.
   *
   * @param $object
   * @return bool
   */
  public function validate($object) {
    if ($object !== true) {
      $this->context->addViolation(
        new Violation(
          self::VIOLATION_TYPE_TRUE,
          $this->options['message']
        )
      );
      return false;
    }
    return true;
  }

}
