<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * A abstract dummy class for testing.
   *
   */
  abstract class AbstractDummy extends Object {

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
      return TRUE;
    }
  }
?>
