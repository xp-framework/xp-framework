<?php namespace validation;

/**
 * While validating, every field has a context in which the violations will be stored.
 * After a field is validated, the field name and violations will be written
 * into the validation result.
 */
class ValidationContext {

  private $fieldName;
  private $violations;

  public function __construct($fieldName) {
    $this->fieldName= $fieldName;
    $this->violations= array();
  }

  /**
   * Returns the field name, this context applies to
   *
   * @return mixed
   */
  public function getFieldName() {
    return $this->fieldName;
  }

  /**
   * @return bool
   */
  public function hasViolations() {
    return count($this->violations) > 0;
  }

  /**
   * @return array
   */
  public function getViolations() {
    return $this->violations;
  }

  /**
   * @param Violation $violation
   */
  public function addViolation(Violation $violation) {
    $this->violations[]= $violation;
  }

}