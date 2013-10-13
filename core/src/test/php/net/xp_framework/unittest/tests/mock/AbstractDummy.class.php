<?php namespace net\xp_framework\unittest\tests\mock;

/**
 * A abstract dummy class for testing.
 *
 */
abstract class AbstractDummy extends \lang\Object {

  /**
   * A concrete method
   *
   * @return  string
   */
  public function concreteMethod() {
    return 'concreteMethod';
  }

  /**
   * An abstract method
   *
   */
  public abstract function abstractMethod();
  
  /**
   * Returns whether a given value is equal to this class
   *
   * @param   lang.Generic cmp
   * @return  bool
   */
  public function equals($cmp) {
    return true;
  }
}
