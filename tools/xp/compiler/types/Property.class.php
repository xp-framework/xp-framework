<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  $package= 'xp.compiler.types';

  /**
   * Represents a property
   *
   * @see      xp://xp.compiler.types.Types
   */
  class xp·compiler·types·Property extends Object {
    public
      $name       = '',
      $type       = NULL,
      $modifiers  = 0;

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
        '%s<%s %s %s>',
        $this->getClassName(),
        implode(' ', Modifiers::namesOf($this->modifiers)),
        $this->type->compoundName(),
        $this->name
      );
    }
  }
?>
