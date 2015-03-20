<?php namespace validation\constraints;

use validation\ConstraintValidator;
use validation\Violation;

/**
 * Class True
 *
 * @author jzinnau
 *
 */
class True extends ConstraintValidator {

  const VIOLATION_TYPE_TRUE= 'true';

  protected function getDefaultOptions() {
    return array(
      'message' => 'validation#true'
    );
  }

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
