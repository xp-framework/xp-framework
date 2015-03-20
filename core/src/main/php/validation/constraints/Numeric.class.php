<?php namespace validation\constraints;

use validation\ConstraintValidator;
use validation\Violation;

/**
 * Class Numerics
 *
 * @author jzinnau
 *
 */
class Numeric extends ConstraintValidator {

  const VIOLATION_TYPE_NUMERIC= 'numeric';

  protected function getDefaultOptions() {
    return array(
      'message' => 'validation#numeric'
    );
  }

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