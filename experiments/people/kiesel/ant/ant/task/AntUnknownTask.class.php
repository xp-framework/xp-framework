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
  class AntUnknownTask extends Object {
    public
      $type     = '';

    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    #[@xmlmapping(element= '.', pass= array('name()'))]
    public function setType($type) {
      $this->type= $type;
    }
  }
?>
