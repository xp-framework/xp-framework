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
    var
      $name=  '',
      $arg=   NULL;

    /**
     * Constructor.
     *
     * @access  public
     */
    function __construct($name, $arg= NULL) {
      $this->name=  $name;
      $this->arg=   $arg;
    }

    /**
     * Set Name
     *
     * @access  public
     * @param   string name
     */
    function setName($name) {
      $this->name= $name;
    }

    /**
     * Get Name
     *
     * @access  public
     * @return  string
     */
    function getName() {
      return $this->name;
    }

    /**
     * Set Arg
     *
     * @access  public
     * @param   &lang.Object arg
     */
    function setArgument(&$arg) {
      $this->arg= &$arg;
    }

    /**
     * Get Arg
     *
     * @access  public
     * @return  &lang.Object
     */
    function &getArgument() {
      return $this->arg;
    }
    
    /**
     * Return the string representation for this event.
     *
     * @access  public
     * @return  string
     */
    function toString() {
      return $this->getClassName().'('.$this->name.') {'.var_export($this->arg, TRUE).'}';
    }
  }
?>
