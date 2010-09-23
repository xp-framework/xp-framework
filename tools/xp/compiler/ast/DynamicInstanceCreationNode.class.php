<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('xp.compiler.ast.Node');

  /**
   * Dynamic instance creation
   *
   * Example
   * ~~~~~~~
   * <code>
   *   $a= new $type();
   * </code>
   *
   * Note
   * ~~~~
   * This is only available in PHP syntax!
   *
   * @see   xp://xp.compiler.ast.InstanceCreationNode
   * @see   php://new
   */
  class DynamicInstanceCreationNode extends xp·compiler·ast·Node {
    public $parameters = NULL;
    public $variable = '';
  }
?>
