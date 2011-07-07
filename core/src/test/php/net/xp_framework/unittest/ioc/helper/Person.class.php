<?php
/* This class is part of the XP framework
 *
 * $Id: Person.class.php 2996 2011-02-13 09:33:11Z mikey $
 */

  /**
   * @purpose  Helper class for test cases.
   */
  #[@ImplementedBy('net.xp_framework.unittest.ioc.helper.Schst')]
  interface Person {
    /**
     * says hello world
     *
     * @return  string
     */
    public function sayHello();
  }
?>
