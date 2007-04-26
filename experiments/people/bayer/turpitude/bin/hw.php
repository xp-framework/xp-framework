<?php
require('/home/nsn/devel/xp.public/trunk/skeleton/lang.base.php');
/* This class is part of the XP framework
 *
 * $Id: HelloWorldBean.class.php 10120 2007-04-25 15:37:41Z friebe $ 
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
  return new HelloWorldBean();
?>

