<?php namespace validation\constraints;

use validation\ConstraintValidator;
use validation\Violation;

/**
 * Checks if a value is a blank string or null
 *
 * Example:
 * ```
 * #[@Assert([
 * # array('type'=>'validation.constraints.Blank')
 * #])]
 * ```
 */
class Blank extends ConstraintValidator {

  const VIOLATION_TYPE_BLANK= 'blank';

  /**
   * Returns default options for this validator
   *
   * @return array
   */
  protected function getDefaultOptions() {
    return array(
      'message' => 'validation#blank'
    );
  }

  /**
   * Returns true if the object is valid.
   *
   * @param $object
   * @return bool
   */
  public function validate($object) {
    if ($object !== null && $object !== '') {
      $this->context->addViolation(
        new Violation(
          self::VIOLATION_TYPE_BLANK,
          $this->options['message']
        )
      );
      return false;
    }
    return true;
  }

}
