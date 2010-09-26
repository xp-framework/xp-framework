<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('xp.compiler.ast.Node');

  /**
   * Represents class access
   *
   * <code>
   *   self::class;
   * </code>
   */
  class ClassAccessNode extends xp·compiler·ast·Node {
    public $type= NULL;
    
    /**
     * Constructor
     *
     * @param   xp.compiler.types.TypeName type
     */
    public function __construct($type= NULL) {
      $this->type= $type;
    }
  }
?>
