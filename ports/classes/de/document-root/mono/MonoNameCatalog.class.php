<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class MonoNameCatalog extends Object {
    var
      $names    = array();

    /**
     * Set Names
     *
     * @access  public
     * @param   mixed[] names
     */
    function setNames($names) {
      $this->names= $names;
    }

    /**
     * Get Names
     *
     * @access  public
     * @return  mixed[]
     */
    function getNames() {
      return $this->names;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function setNameFor($id, $name) {
      $this->names[$id]= $name;
    }
  }
?>
