<?php namespace validation\constraints;

use validation\ConstraintValidator;
use validation\Violation;

/**
 * Checks the lenght of a string value.
 * The allowed length can be set by the 'min' and 'max' parameters.
 * If one parameter is set to null, this constraint will be ignored.
 *
 * Example:
 * ```
 * #[@Assert([
 * # array('type'=>'validation.constraints.Length', 'min'=>1, 'max'=>100)
 * #])]
 * ```
 */
class Length extends ConstraintValidator {

  const VIOLATION_TYPE_TOOLONG= 'length_toolow';
  const VIOLATION_TYPE_TOOSHORT= 'length_tooshort';

  /**
   * Returns default options for this validator
   *
   * @return array
   */
  protected function getDefaultOptions() {
    return array(
      'min'             => 0,
      'max'             => 100,
      'messageTooLong'  => 'validation#length.toolong',
      'messageTooShort' => 'validation#length.tooshort'
    );
  }

  /**
   * Returns true if the object is valid.
   *
   * @param $object
   * @return bool
   */
  public function validate($object) {
    if (mb_strlen($object, \XP::ENCODING) < $this->options['min']) {
      $this->context->addViolation(
        new Violation(
          self::VIOLATION_TYPE_TOOSHORT,
          $this->options['messageTooShort']
        )
      );
      return false;
    }
    if (mb_strlen($object, \XP::ENCODING) > $this->options['max']) {
      $this->context->addViolation(
        new Violation(
          self::VIOLATION_TYPE_TOOLONG,
          $this->options['messageTooLong']
        )
      );
      return false;
    }
    return true;
  }

}