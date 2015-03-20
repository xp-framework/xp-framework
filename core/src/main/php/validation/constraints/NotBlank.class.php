<?php namespace validation\constraints;

use validation\ConstraintValidator;
use validation\Violation;

/**
 * Checks if a value is not null and not an empty string
 *
 * Example:
 * ```
 * #[@Assert([
 * # array('type'=>'validation.constraints.NotBlank')
 * #])]
 * ```
 */
class NotBlank extends ConstraintValidator {

  const VIOLATION_TYPE_NOTBLANK= 'notblank';

  /**
   * Returns default options for this validator
   *
   * @return array
   */
  protected function getDefaultOptions() {
    return array(
      'message' => 'validation#notblank'
    );
  }

  /**
   * Returns true if the object is valid.
   *
   * @param $object
   * @return bool
   */
  public function validate($object) {
    if ($object === null || $object === '') {
      $this->context->addViolation(
        new Violation(
          self::VIOLATION_TYPE_NOTBLANK,
          $this->options['message']
        )
      );
      return false;
    }
    return true;
  }

}
