<?php namespace net\xp_framework\unittest\core\generics;

/**
 * List of elements
 *
 */
#[@generic(self= 'T')]
class NSListOf extends \lang\Object {
  public $elements= array();

  /**
   * Constructor
   *
   * @param   T... initial
   */
  #[@generic(params= 'T...')]
  public function __construct() {
    $this->elements= func_get_args();
  }

  /**
   * Adds an element
   *
   * @param   T... elements
   * @return  self
   */
  #[@generic(params= 'T...')]
  public function withAll() {
    $args= func_get_args();
    $this->elements= array_merge($this->elements, $args);
    return $this;
  }

  /**
   * Returns a list of all elements
   *
   * @return  T[] elements
   */
  #[@generic(return= 'T[]')]
  public function elements() {
    return $this->elements;
  }
}