<?php namespace net\xp_framework\unittest\core\generics;

/**
 * Nullable value
 *
 */
#[@generic(self= 'T')]
class Nullable extends \lang\Object {
  protected $value;

  /**
   * Constructor
   *
   * @param   T value
   */
  #[@generic(params= 'T')]
  public function __construct($value= null) {
    $this->value= $value;
  }

  /**
   * Returns whether a value exists
   *
   * @return  bool
   */
  public function hasValue() {
    return $this->value !== null;
  }

  /**
   * Sets value
   *
   * @param   T value
   * @return  self this instance
   */
  #[@generic(params= 'T')]
  public function set($value= null) {
    $this->value= $value;
    return $this;
  }

  /**
   * Returns value
   *
   * @return  T value
   */
  #[@generic(return= 'T')]
  public function get() {
    return $this->value;
  }
}
