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
      $value  = NULL;
    
    /**
     * Get type of message
     *
     * @return  int
     */
    public abstract function getType();    

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
