<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('xp.compiler.ast.Node');

  /**
   * Switch statement
   *
   * @purpose  purpose
   */
  class SwitchNode extends xp·compiler·ast·Node {
    public $expression = NULL;
    public $cases      = array();
  }
?>
