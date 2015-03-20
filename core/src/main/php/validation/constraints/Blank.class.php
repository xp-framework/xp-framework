<?php namespace validation\constraints;

use validation\ConstraintValidator;
use validation\Violation;

/**
 * Class Blank
 *
 * @author jzinnau
 *
 */
class Blank extends ConstraintValidator {

  const VIOLATION_TYPE_BLANK= 'blank';

  protected function getDefaultOptions() {
    return array(
      'message' => 'validation#blank'
    );
  }

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
