<?php namespace validation\constraints;

use validation\ConstraintValidator;
use validation\Violation;

/**
 * Class NotNull
 *
 * @author jzinnau
 *
 */
class NotNull extends ConstraintValidator {

  const VIOLATION_TYPE_NOTNULL= 'notnull';

  protected function getDefaultOptions() {
    return array(
      'message' => 'validation#notnull'
    );
  }

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
