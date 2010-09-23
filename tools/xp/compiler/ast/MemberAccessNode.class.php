<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('xp.compiler.ast.Node');

  /**
   * Represents a member access
   *
   * <code>
   *   $this.member;
   * </code>
   */
  class MemberAccessNode extends xp·compiler·ast·Node {
    public $target= NULL;
    public $name= '';
    
    /**
     * Constructor
     *
     * @param   xp.compiler.ast.Node target
     * @param   string name
     */
    public function __construct($target= NULL, $name= '') {
      $this->target= $target;
      $this->name= $name;
    }
  }
?>
