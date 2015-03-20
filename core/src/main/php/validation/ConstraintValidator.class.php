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

  protected function getDefaultOptions() {
    return array();
  }

  public function continueValidationAfterViolation() {
    return $this->continue;
  }

  public abstract function validate($object);

}