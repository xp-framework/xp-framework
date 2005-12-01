<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Represents a "numeric" array
   *
   * @purpose  Wrapper
   */
  class ArrayList extends Object {
    var
      $values=  NULL;
    
    /**
     * Constructor
     *
     * @access  public
     * @param   mixed[] values default array()
     */
    function __construct($values= array()) {
      $this->values= $values;
    }
  }
?>
