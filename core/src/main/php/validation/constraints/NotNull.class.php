<?php namespace validation\constraints;

use validation\ConstraintValidator;
use validation\Violation;

/**
 * Checks if a value is not null
 *
 * Example:
 * ```
 * #[@Assert([
 * # array('type'=>'validation.constraints.NotNull')
 * #])]
 * ```
 */
class NotNull extends ConstraintValidator {

  const VIOLATION_TYPE_NOTNULL= 'notnull';

  /**
   * Returns default options for this validator
   *
   * @return array
   */
  protected function getDefaultOptions() {
    return array(
      'message' => 'validation#notnull'
    );
  }

  /**
   * Returns true if the object is valid.
   *
   * @param $object
   * @return bool
   */
  public function validate($object) {
    if ($object === null) {
      $this->context->addViolation(
        new Violation(
          self::VIOLATION_TYPE_NOTNULL,
          $this->options['message']
        )
      );
      return false;
    }
    return true;
  }

}
