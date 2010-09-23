<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('xp.compiler.ast.Node');
  
  /**
   * Represents a method call
   *
   * <code>
   *   $this.connect();
   * </code>
   */
  class MethodCallNode extends xp·compiler·ast·Node {
    public $target= NULL;
    public $name= '';
    public $arguments= array();
    
    /**
     * Creates a new InvocationNode object
     *
     * @param   xp.compiler.ast.Node target
     * @param   string name
     * @param   xp.compiler.ast.Node[] arguments
     */
    public function __construct($target= NULL, $name= '', $arguments= NULL) {
      $this->target= $target;
      $this->name= $name;
      $this->arguments= $arguments;
    }
  }
?>
