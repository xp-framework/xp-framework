<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('xp.compiler.ast.Node');

  /**
   * Represents a static member access
   *
   * <code>
   *   self::$member;
   * </code>
   */
  class StaticMemberAccessNode extends xp·compiler·ast·Node {
    public $type= NULL;
    public $name= '';
    
    /**
     * Constructor
     *
     * @param   xp.compiler.types.TypeName type
     * @param   string name
     */
    public function __construct($type= NULL, $name= '') {
      $this->type= $type;
      $this->name= $name;
    }
  }
?>
