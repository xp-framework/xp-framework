<?php namespace validation;

/**
 * Interface ConstraintValidator
 *
 * @author jzinnau
 *
 */
abstract class ConstraintValidator {

  protected $context;
  protected $options;
  protected $continue;

  public function __construct(ValidationContext $context, array $params) {
    $this->context= $context;
    $this->options= array_merge($this->getDefaultOptions(), $params);
    $this->continue= isset($this->options['continue']) && $this->options['continue'];
  }

  /**
   * Overwrite to define the options for the validator
   *
   * @return array
   */
  protected function getDefaultOptions() {
    return array();
  }

  /**
   * Returns true if the validation should continue after one constraint faild for a field
   *
   * @return bool
   */
  public function continueValidationAfterViolation() {
    return $this->continue;
  }

  /**
   * Returns true if the object is valid.
   *
   * @param $object
   * @return bool
   */
  public abstract function validate($object);

}