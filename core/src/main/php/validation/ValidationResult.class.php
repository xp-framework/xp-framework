<?php namespace validation;

use lang\Object;

/**
 * Class validationResult
 *
 * @author jzinnau
 *
 */
class ValidationResult extends Object {

  private $violations;

  public function __construct() {
    $this->violations= array();
  }

  public function isValid() {
    return count($this->violations) == 0;
  }

  public function addContextResults(ValidationContext $context) {
    if (!isset($this->violations[$context->getFieldName()])) {
      $this->violations[$context->getFieldName()]= array();
    }
    $this->violations[$context->getFieldName()]= array_merge(
      $this->violations[$context->getFieldName()],
      $context->getViolations()
    );
  }

  public function getViolations() {
    return $this->violations;
  }

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