<?php
/* This class is part of the XP framework's experiments
 *
 * $Id$
 */
 
  uses('ComClassLoader');
  
  /**
   * ActiveXObject class
   *
   * @ext      com
   * @see      http://www.webreference.com/js/column55/activex.html
   * @purpose  COM Wrapper
   */
  class ActiveXObject extends Object {
    var
      $var = NULL;
    
    /**
     * Destructor. Releases the com automation handle.
     *
     * @access  public
     */
    function __destruct() {
      if ($this->var) {
        com_release($this->var);
        $this->var= NULL;
      }
    }
  }
?>
