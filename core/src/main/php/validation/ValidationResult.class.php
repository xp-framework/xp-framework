<?php namespace validation;

use lang\Object;

/**
 * ValidationResult object
 */
class ValidationResult extends Object {

  private $violations;
  private $isValid;

  public function __construct() {
    $this->violations= array();
  }

  /**
   * Indicate, that the validation has faild
   */
  public function setInvalid() {
    $this->isValid= false;
  }

  /**
   * Indicate, that the validation has succeeded
   */
  public function setValid() {
    $this->isValid= true;
  }

  /**
   * Returns true if there where violations against the constraints of an object.
   * This can be true, even if there are no actual violation objects (this happens when
   * a validator isn't returning a violation).
   *
   * @return mixed
   */
  public function isValid() {
    return $this->isValid;
  }

  /**
   * Adds the violations of a validation context to the result
   *
   * @param ValidationContext $context
   */
  public function addContextResults(ValidationContext $context) {
    if ($context->hasViolations()) {
      $this->setInvalid();
      if (!isset($this->violations[$context->getFieldName()])) {
        $this->violations[$context->getFieldName()]= array();
      }
      $this->violations[$context->getFieldName()]= array_merge(
        $this->violations[$context->getFieldName()],
        $context->getViolations()
      );
    }
  }

  /**
   * Returns all violation objects sorted by the field names
   *
   * @return array
   */
  public function getViolations() {
    return $this->violations;
  }

  /**
   * Returns the array representation of all violation objects sorted by the field names
   *
   * @return array
   */
  public function getViolationsArray() {
    $return= array();

    foreach ($this->violations as $field => $violations) {
      $return[$field]= array();
      foreach ($violations as $violation) {
        $return[$field][]= $violation->toArray();
      }
    }

    return $return;
  }

}