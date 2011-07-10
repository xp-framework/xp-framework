<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'ioc.InjectionProvider',
    'net.xp_framework.unittest.ioc.helper.Schst'
  );

  /**
   * @purpose  Helper class for test cases.
   */
  class DeveloperProvider extends Object implements InjectionProvider {
    /**
     * returns the value to provide
     *
     * @param   string  $name  optional
     * @return  mixed
     */
    public function get($name = NULL) {
      return new Schst();
    }
  }
?>
