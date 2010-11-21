<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('xp.compiler.types.Scope');

  /**
   * Represents the method scope
   *
   * @see     xp://xp.compiler.Scope
   */
  class MethodScope extends Scope {
    public $name= NULL;
  
    /**
     * Constructor
     *
     * @param   string name
     */
    public function __construct($name= NULL) {
      $this->name= $name;
      parent::__construct();
    }
  }
?>
