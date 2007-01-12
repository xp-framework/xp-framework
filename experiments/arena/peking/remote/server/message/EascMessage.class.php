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
  abstract class EascMessage extends Object {
    public
      $type   = NULL,
      $value  = NULL;

    /**
     * Set Type
     *
     * @param   lang.Object type
     */
    public function setType($type) {
      $this->type= $type;
    }

    /**
     * Get Type
     *
     * @return  lang.Object
     */
    public function getType() {
      return $this->type;
    }

    /**
     * Set Value
     *
     * @param   lang.Object value
     */
    public function setValue($value) {
      $this->value= $value;
    }

    /**
     * Get Value
     *
     * @return  lang.Object
     */
    public function getValue() {
      return $this->value;
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function handle($protocol, $data) { }
  }
?>
