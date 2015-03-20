<?php namespace validation;

/**
 * Class Violation
 *
 * @author jzinnau
 *
 */
class Violation {

  private $code;
  private $message;

  public function __construct($code, $message) {
    $this->code= $code;
    $this->message= $message;
  }

  /**
   * @return string
   */
  public function getCode() {
    return $this->code;
  }

  /**
   * @param string $code
   */
  public function setCode($code) {
    $this->code= $code;
  }

  /**
   * @return string
   */
  public function getMessage() {
    return $this->message;
  }

  /**
   * @param string $message
   */
  public function setMessage($message) {
    $this->message= $message;
  }

  public function toArray() {
    return array(
      'code' => $this->getCode(),
      'message' => $this->getMessage()
    );
  }

}