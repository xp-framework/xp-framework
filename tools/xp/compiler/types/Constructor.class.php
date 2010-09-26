<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  $package= 'xp.compiler.types';

  /**
   * Represents a constructor
   *
   * @see      xp://xp.compiler.types.Types
   */
  class xp·compiler·types·Constructor extends Object {
    public
      $modifiers  = 0,
      $parameters = array(),
      $holder     = NULL;

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
        '%s<%s __construct(%s)>',
        $this->getClassName(),
        implode(' ', Modifiers::namesOf($this->modifiers)),
        substr($signature, 2)
      );
    }
  }
?>
