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
     * @access  public
     */
    public function __construct($name, $arg= NULL) {
      $this->name=  $name;
      $this->arg=   $arg;
    }

    /**
     * Set Name
     *
     * @access  public
     * @param   string name
     */
    public function setName($name) {
      $this->name= $name;
    }

    /**
     * Get Name
     *
     * @access  public
     * @return  string
     */
    public function getName() {
      return $this->name;
    }

    /**
     * Set Arg
     *
     * @access  public
     * @param   &lang.Object arg
     */
    public function setArgument(&$arg) {
      $this->arg= &$arg;
    }

    /**
     * Get Arg
     *
     * @access  public
     * @return  &lang.Object
     */
    public function &getArgument() {
      return $this->arg;
    }
    
    /**
     * Return the string representation for this event.
     *
     * @access  public
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'('.$this->name.') {'.var_export($this->arg, TRUE).'}';
    }
  }
?>
