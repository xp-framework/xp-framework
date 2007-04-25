<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Says hello
   *
   * @purpose  Bean
   */
  #[@bean(type = STATELESS, name = 'xp/peanuts/HelloWorld')]
  class HelloWorldBean extends Object {

    /**
     * Runs a test
     *
     * @param   string to
     * @return  string greeting
     */ 
    #[@remote]
    public function sayHello($to) {
      return $this->getClassName().' says hello to '.$to;
    }
  }
?>
