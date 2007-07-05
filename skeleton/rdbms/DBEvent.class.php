<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Generic DB event.
   *
   * @purpose  Wrap database events
   */
  class DBEvent extends Object {
    public
      $name=  '',
      $arg=   NULL;

    /**
     * Constructor.
     *
     */
    public function __construct($name, $arg= NULL) {
      $this->name=  $name;
      $this->arg=   $arg;
    }

    /**
     * Set Name
     *
     * @param   string name
     */
    public function setName($name) {
      $this->name= $name;
    }

    /**
     * Get Name
     *
     * @return  string
     */
    public function getName() {
      return $this->name;
    }

    /**
     * Set Arg
     *
     * @param   lang.Object arg
     */
    public function setArgument($arg) {
      $this->arg= $arg;
    }

    /**
     * Get Arg
     *
     * @return  lang.Object
     */
    public function getArgument() {
      return $this->arg;
    }
    
    /**
     * Return the string representation for this event.
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'('.$this->name.') {'.xp::stringOf($this->arg).'}';
    }
  }
?>
