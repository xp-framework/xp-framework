<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  $package= 'xp.compiler.types';

  /**
   * Represents an operator
   *
   * @see      xp://xp.compiler.types.Types
   */
  class xp·compiler·types·Operator extends Object {
    public
      $symbol     = '',
      $returns    = NULL,
      $modifiers  = 0,
      $parameters = array(),
      $holder     = NULL;

    /**
     * Constructor
     *
     * @param   string symbol
     */
    public function __construct($symbol= '') {
      $this->symbol= $symbol;
    }

    /**
     * Returns name
     *
     * @return  string
     */
    public function name() {
      return $this->symbol;
    }

    /**
     * Creates a string representation of this Operator
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
        $this->symbol,
        substr($signature, 2)
      );
    }
  }
?>
