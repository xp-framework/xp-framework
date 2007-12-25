<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Unknown remote object
   *
   * @ext      overload
   * @see      php://__PHP_Incomplete_Class
   * @test     xp://net.xp_framework.unittest.remote.UnknownRemoteObjectTest
   * @purpose  purpose
   */
  class UnknownRemoteObject extends Object {
    public
      $__name     = '',
      $__members  = array();

    /**
     * Constructor
     *
     * @param   string name
     * @param   array<string, mixed> members default array()
     */
    public function __construct($name, $members= array()) {
      $this->__name= $name;
      $this->__members= $members;
    }

    /**
     * Creates a string representation of this object
     *
     * @return  string
     */
    public function toString() {
      $s= $this->getClassName().'@('.$this->__name.") {\n";
      foreach (array_keys($this->__members) as $member) {
        $s.= sprintf("  [%-20s] %s\n", $member, xp::stringOf($this->__members[$member]));
      }
      return $s.'}';
    }

    /**
     * Member set interceptor
     *
     * @param   string name
     * @param   mixed value
     * @throws  lang.IllegalAccessException
     */
    public function __set($name, $value) {
      throw new IllegalAccessException('Access to undefined member "'.$name.'"');
    }
    
    /**
     * Member get interceptor
     *
     * @param   string name
     * @throws  lang.IllegalAccessException
     */
    public function __get($name) {
      throw new IllegalAccessException('Access to undefined member "'.$name.'"');
    }
  
    /**
     * Method call interceptor
     *
     * @param   string name
     * @param   mixed[] args
     * @throws  lang.IllegalAccessException
     */
    public function __call($name, $args) {
      throw new IllegalAccessException('Cannot call method "'.$name.'" on an unknown remote object');
    }
  }
?>
