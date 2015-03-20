<?php namespace validation\constraints;

use validation\ConstraintValidator;
use validation\Violation;

/**
 * Class Null
 *
 * @author jzinnau
 *
 */
class Null extends ConstraintValidator {

  const VIOLATION_TYPE_NULL= 'null';

  protected function getDefaultOptions() {
    return array(
      'message' => 'validation#null'
    );
  }

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
