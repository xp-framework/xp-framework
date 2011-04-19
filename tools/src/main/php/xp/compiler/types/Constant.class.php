<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'xp.compiler.types';

  /**
   * Represents a constant
   *
   * @see      xp://xp.compiler.types.Types
   */
  class xp·compiler·types·Constant extends Object {
    public
      $name       = '',
      $type       = NULL,
      $value      = NULL;

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
     * Creates a string representation of this field
     *
     * @return  string
     */
    public function toString() {
      return sprintf(
        '%s<const %s %s= %s>',
        $this->getClassName(),
        $this->type->compoundName(),
        $this->name,
        xp::stringOf($this->value)
      );
    }
  }
?>
