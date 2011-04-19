<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('xp.compiler.ast.Node');

  /**
   * Represents a list of statements
   *
   */
  class StatementsNode extends xp·compiler·ast·Node {
    public $list= array();
    
    /**
     * Constructor.
     *
     * @param   xp.compiler.ast.Node[] initial
     */
    public function __construct(array $initial= array()) {
      $this->list= $initial;
    }
  }
?>
