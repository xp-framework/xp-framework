<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('xp.compiler.ast.Node');

  /**
   * (Insert class' description here)
   *
   * @purpose  purpose
   */
  class UnaryOpNode extends xp·compiler·ast·Node {
    public $postfix = FALSE;
    public $expression = NULL;
    public $op = NULL;
  }
?>
