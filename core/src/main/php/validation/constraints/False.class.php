<?php namespace validation\constraints;

use validation\ConstraintValidator;
use validation\Violation;

/**
 * Class False
 *
 * @author jzinnau
 *
 */
class False extends ConstraintValidator {

  const VIOLATION_TYPE_FALSE= 'false';

  protected function getDefaultOptions() {
    return array(
      'message' => 'validation#false'
    );
  }

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
