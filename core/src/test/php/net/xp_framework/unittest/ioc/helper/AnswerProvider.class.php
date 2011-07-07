<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'ioc.InjectionProvider',
    'net.xp_framework.unittest.ioc.helper.Answer'
  );

  /**
   * @purpose  Helper class for test cases.
   */
  class AnswerProvider extends Object implements InjectionProvider {
    /**
     * returns the value to provide
     *
     * @param   string  $name  optional
     * @return  mixed
     */
    public function get($name = null) {
      return new Answer();
    }
  }
?>
