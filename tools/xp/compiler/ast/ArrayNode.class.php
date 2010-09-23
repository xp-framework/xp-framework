<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('xp.compiler.ast.Node', 'xp.compiler.ast.Resolveable');

  /**
   * Represents an array literal
   * 
   * Examples:
   * <code>
   *   $a= [1, 2, 3];
   *   $a= new string[] { "Hello", "Hallo", "Grüezi" };
   * </code>
   *
   * @test    xp://net.xp_lang.tests.syntax.xp.ArrayTest
   */
  class ArrayNode extends xp·compiler·ast·Node implements Resolveable {
    public $type;
    public $values;

    /**
     * Resolve this node's value.
     *
     * @return  var
     */
    public function resolve() {
      $resolved= array();
      foreach ($this->values as $i => $value) {
        if (!$value instanceof Resolveable) {
          throw new IllegalStateException('Value at offset '.$i.' is not resolveable: '.xp::stringOf($value));
        }
        $resolved[]= $value->resolve();
      }
      return $resolved;
    }
  }
?>
