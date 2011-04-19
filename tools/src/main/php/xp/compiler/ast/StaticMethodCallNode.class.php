<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('xp.compiler.ast.Node');
  
  /**
   * Represents a static method call
   *
   * <code>
   *   self::connect();
   * </code>
   */
  class StaticMethodCallNode extends xp·compiler·ast·Node {
    public $type= NULL;
    public $name= '';
    public $arguments= array();
    
    /**
     * Creates a new InvocationNode object
     *
     * @param   xp.compiler.types.TypeName type
     * @param   string name
     * @param   xp.compiler.ast.Node[] arguments
     */
    public function __construct($type= NULL, $name= '', $arguments= NULL) {
      $this->type= $type;
      $this->name= $name;
      $this->arguments= $arguments;
    }
  }
?>
