<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Represents a method's argument
   *
   * @see      xp://lang.reflect.Routine#getArguments
   * @purpose  Reflection
   */
  class Argument extends Object {
    public
      $name     = '',
      $type     = '',
      $optional = FALSE;

    /**
     * Constructor
     *
     * @access  public
     * @param   string name
     * @param   string type default 'mixed'
     * @param   bool optional default FALSE
     */    
    public function __construct($name, $type= 'mixed', $optional= FALSE) {
      $this->name= $name;
      $this->type= $type;
      $this->optional= $optional;
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
     * Get Type
     *
     * @access  public
     * @return  string
     */
    public function getType() {
      return $this->type;
    }

    /**
     * Retrieve whether this argument is optional
     *
     * @access  public
     * @return  bool
     */
    public function isOptional() {
      return $this->optional;
    }
  }
?>
