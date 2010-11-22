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

    /**
     * Returns a hashcode
     *
     * @return  string
     */
    public function hashCode() {
      return '$'.$this->target->hashCode().'->'.$this->name;
    }
    
    /**
     * Returns whether another object equals this.
     *
     * @param   lang.Generic cmp
     * @return  bool
     */
    public function equals($cmp) {
      return 
        $cmp instanceof self && 
        $this->target->equals($cmp->target) &&
        $this->name === $cmp->name
      ;
    }
  }
?>
