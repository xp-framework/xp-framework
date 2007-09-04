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
  class Scatter extends Object {
    protected
      $values;
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function __construct(array $values) {
      $this->values= $this->values;
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function getRange() {
      return (
        Aggregation::$MAXIMUM->calculate($this->values)- 
        Aggregation::$MINIMUM->calculate($this->values)
      );
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function interQuartileRange() {
      $q= new Quartile($this->values);
      return ($q->quartileAt(0.75)- $q->quartileAt(0.25));
    }
    
    /**
     * Mean deviation from the median
     *
     * @param   
     * @return  
     */
    public function meanDeviation() {
      $median= Aggregation::$MEDIAN->calculate($this->values);
      
      $sum= 0;
      foreach ($this->values as $v) { $sum+= abs($median- $v); }
      
      return $sum / sizeof($this->values);
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function medianAbsoluteDeviation() {
      $median= Aggregation::$MEDIAN->calculate($this->values);
      
      $sum= 0;
      foreach ($this->values as $v) { $sum+= abs($median- $v); }
      
      return $median * $sum;
    }
  }
?>
