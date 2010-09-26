<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('xp.compiler.ast.Node');

  /**
   * Represents an array access operator
   *
   * Examples:
   * <code>
   *   $first= $list[0];
   *   $element= $map[$key];
   * </code>   
   */
  class ArrayAccessNode extends xp·compiler·ast·Node {
    public $target= NULL;
    public $offset= NULL;
    
    /**
     * Constructor
     *
     * @param   xp.compiler.ast.Node target
     * @param   xp.compiler.ast.Node offset
     */
    public function __construct($target= NULL, xp·compiler·ast·Node $offset= NULL) {
      $this->target= $target;
      $this->offset= $offset;
    }
    
    /**
     * Returns a hashcode
     *
     * @return  string
     */
    public function hashCode() {
      return '['.($this->offset ? $this->offset->hashCode() : '').']';
    }
  }
?>
