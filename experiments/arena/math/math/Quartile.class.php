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
  class Quartile extends Object {
    protected
      $values;
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function __construct(array $values) {
      $this->values= $values;
      sort($this->values);
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function quartileAt($p) {
      if (0 > $p || $p > 1) throw new IllegalArgumentException('p must be 0 < p < 1 to retrieve the quartile.');
      
      $element= intval(sizeof($this->values) * $p)- 1;
      return $this->values[$element];
    }
  }
?>
