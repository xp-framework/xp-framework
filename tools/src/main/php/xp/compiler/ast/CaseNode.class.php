<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('xp.compiler.ast.Node');

  /**
   * Switch statement: Case
   *
   * @purpose  purpose
   */
  class CaseNode extends xp·compiler·ast·Node {
    public
      $expression,
      $statements;
    
  }
?>
