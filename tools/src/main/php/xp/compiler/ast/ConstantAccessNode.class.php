<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('xp.compiler.ast.Node');

  /**
   * Represents constant access
   *
   * <code>
   *   self::SEPARATOR;
   * </code>
   */
  class ConstantAccessNode extends xp·compiler·ast·Node {
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
