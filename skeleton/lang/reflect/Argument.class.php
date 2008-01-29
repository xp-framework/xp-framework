<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Represents a method's argument
   *
   * @deprecated Use lang.reflect.Parameter instead
   * @see      xp://lang.reflect.Routine#getArguments
   * @purpose  Reflection
   */
  class Argument extends Object {
    public
      $name     = '',
      $types    = array(),
      $optional = FALSE,
      $default  = NULL;

    /**
     * Constructor
     *
     * @param   string name
     * @param   string[] types default array
     * @param   bool optional default FALSE
     * @param   string default default NULL
     */    
    public function __construct($name, $types= array(), $optional= FALSE, $default= NULL) {
      $this->name= $name;
      $this->types= $types;
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
     * @param   bool hinted default FALSE
     * @return  string
     */
    public function getType($hinted= FALSE) {
      return $this->types[0+ (int)$hinted];
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

    /**
     * Get default value.
     *
     * @throws  lang.IllegalStateException in case this argument is not optional
     * @return  mixed
     */
    public function getDefaultValue() {
      if ($this->optional) return $this->default;

      throw new IllegalStateException('Argument "'.$this->name.'" has no default value');
    }
  }
?>
