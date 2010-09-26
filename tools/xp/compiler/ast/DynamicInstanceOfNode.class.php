<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('xp.compiler.ast.Node');

  /**
   * Represents a dynamic instanceof check, where the type is stored in
   * a variable (which must then at runtime either be a string with the
   * type literal or an object):
   *
   * Examples
   * ~~~~~~~~
   * <code>
   *   // Test whether $a is an instance of the type named "Object"
   *   $type= 'Object';
   *   $a instanceof $type;
   *
   *   // Test whether $other is an instance of this instance
   *   $other instanceof $this;
   * </code>
   * 
   * Note
   * ~~~~
   * This is only available in PHP syntax!
   *
   * @see   xp://xp.compiler.ast.InstanceOfNode
   * @see   php://instanceof
   */
  class DynamicInstanceOfNode extends xp·compiler·ast·Node {
    public $expression= NULL;
    public $variable= '';
  }
?>
