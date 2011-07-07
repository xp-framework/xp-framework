<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('ioc.InjectionProvider');

  /**
   * @purpose  Helper class for test cases.
   */
  class InjectorAnswerConstantProvider extends Object implements InjectionProvider {
    /**
     * returns the value to provide
     *
     * @param   string  $name  optional
     * @return  mixed
     */
    public function get($name = null) {
      return 42;
    }
  }
?>