<?php namespace validation\constraints;

use validation\ConstraintValidator;
use validation\Violation;

/**
 * Class NotBlank
 *
 * @author jzinnau
 *
 */
class NotBlank extends ConstraintValidator {

  const VIOLATION_TYPE_NOTBLANK= 'notblank';

  protected function getDefaultOptions() {
    return array(
      'message' => 'validation#notblank'
    );
  }

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
