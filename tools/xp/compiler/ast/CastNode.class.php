<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('xp.compiler.ast.Node');

  /**
   * Cast
   *
   * @purpose  purpose
   */
  class CastNode extends xp·compiler·ast·Node {
    public $type       = NULL;
    public $expression = NULL;
    public $check      = TRUE;
  }
?>
