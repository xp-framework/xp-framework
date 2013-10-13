<?php namespace net\xp_framework\unittest\webservices\rest;



/**
 * Issues
 *
 */
abstract class ConstructorFixture extends \lang\Object {
  public $id= 0;

  /**
   * Check whether another object is equal to this
   * 
   * @param   var cmp
   * @return  bool
   */
  public function equals($cmp) {
    return $cmp instanceof self && $cmp->id === $this->id;
  }

  /**
   * Creates a string representation
   *
   * @return  string
   */
  public function toString() {
    return $this->getClassName().'@'.$this->id;
  }
}
