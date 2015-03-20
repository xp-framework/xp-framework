<?php namespace validation;

/**
 * Class FieldValidationContext
 *
 * @author jzinnau
 *
 */
class ValidationContext {

  private $fieldName;
  private $violations;

  public function __construct($fieldName) {
    $this->fieldName= $fieldName;
    $this->violations= array();
  }

  public function getFieldName() {
    return $this->fieldName;
  }

  public function hasViolations() {
    return count($this->violations) > 0;
  }

  public function getViolations() {
    return $this->violations;
  }

  public function addViolation(Violation $violation) {
    $this->violations[]= $violation;
  }

}