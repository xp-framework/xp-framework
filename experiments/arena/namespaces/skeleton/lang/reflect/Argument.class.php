<?php
/* This class is part of the XP framework
 *
 * $Id: Argument.class.php 10532 2007-06-07 14:43:54Z friebe $ 
 */

  namespace lang::reflect;

  /**
   * Represents a method's argument
   *
   * @see      xp://lang.reflect.Routine#getArguments
   * @purpose  Reflection
   */
  class Argument extends lang::Object {
    public
      $name     = '',
      $type     = '',
      $optional = FALSE,
      $default  = NULL;

    /**
     * Constructor
     *
     * @param   string name
     * @param   string type default 'mixed'
     * @param   bool optional default FALSE
     * @param   string default default NULL
     */    
    public function __construct($name, $type= 'mixed', $optional= FALSE, $default= NULL) {
      $this->name= $name;
      $this->type= $type;
      $this->optional= $optional;
      $this->default= $default;
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
     * Get Type
     *
     * @return  string
     */
    public function getType() {
      return ltrim($this->type, '&');
    }

    /**
     * Returns whether this argument is passed by reference
     *
     * @return  bool
     */
    #[@deprecated]
    public function isPassedByReference() {
      return '&' == $this->type{0};
    }

    /**
     * Retrieve whether this argument is optional
     *
     * @return  bool
     */
    public function isOptional() {
      return $this->optional;
    }

    /**
     * Get default value as a string ("NULL" for NULL). Returns FALSE if
     * no default value is set.
     *
     * @return  string
     */
    public function getDefault() {
      return $this->optional ? var_export($this->default, TRUE) : FALSE;
    }
  }
?>
