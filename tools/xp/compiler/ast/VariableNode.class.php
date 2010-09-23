<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('xp.compiler.ast.Node');

  /**
   * Represents a variable
   *
   */
  class VariableNode extends xp·compiler·ast·Node {
    public
      $name    = '';
    
    /**
     * Constructor
     *
     * @param   string name
     */
    public function __construct($name= '') {
      $this->name= $name;
    }
    
    /**
     * Returns a hashcode
     *
     * @return  string
     */
    public function hashCode() {
      return '$'.$this->name;
    }
  }
?>
