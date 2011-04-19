<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'xp.compiler.types';

  /**
   * Represents a method
   *
   * @see      xp://xp.compiler.types.Types
   */
  class xp·compiler·types·Method extends Object {
    public
      $name       = '',
      $returns    = NULL,
      $modifiers  = 0,
      $parameters = array(),
      $holder     = NULL;

    /**
     * Constructor
     *
     * @param   string name
     */
    public function __construct($name= '') {
      $this->name= $name;
    }

    /**
     * Returns name
     *
     * @return  string
     */
    public function name() {
      return $this->name;
    }

    /**
     * Creates a string representation of this method
     *
     * @return  string
     */
    public function toString() {
      $signature= '';
      foreach ($this->parameters as $parameter) {
        $signature.= ', '.$parameter->compoundName();
      }
      return sprintf(
        '%s<%s %s %s(%s)>',
        $this->getClassName(),
        implode(' ', Modifiers::namesOf($this->modifiers)),
        $this->returns->compoundName(),
        $this->name,
        substr($signature, 2)
      );
    }
  }
?>
