<?php
/* This class is part of the XP framework
 *
 * $Id: Schst.class.php 3001 2011-02-14 19:22:33Z mikey $
 */

  uses(
    'net.xp_framework.unittest.ioc.helper.Developer',
    'net.xp_framework.unittest.ioc.helper.Person'
  );

  /**
   * @purpose  Helper class for test cases.
   */
  class Schst extends Object implements Person, Developer {
    /**
     * says hello world
     *
     * @return  string
     */
    public function sayHello() {
      return "My name is schst.";
    }

    /**
     * does some coding
     */
    public function code() {

    }
  }
?>
