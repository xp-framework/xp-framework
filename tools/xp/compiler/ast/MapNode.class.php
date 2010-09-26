<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('xp.compiler.ast.Node', 'xp.compiler.ast.Resolveable');

  /**
   * Represents a map literal
   *
   */
  class MapNode extends xp·compiler·ast·Node implements Resolveable {
    public $type;
    public $elements;

    /**
     * Resolve this node's value.
     *
     * @return  var
     */
    public function resolve() {
      $resolved= array();
      foreach ($this->elements as $i => $pair) {
        if (!$pair[0] instanceof Resolveable || !$pair[1] instanceof Resolveable) {
          throw new IllegalStateException('Pair at offset '.$i.' is not resolveable: '.xp::stringOf($pair));
        }
        $resolved[$pair[0]->resolve()]= $pair[1]->resolve();
      }
      return $resolved;
    }
  }
?>
