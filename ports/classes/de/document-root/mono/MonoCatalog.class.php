<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('lang.ElementNotFoundException');

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class MonoCatalog extends Object {
    var
      $current_id       = NULL,
      $last_id          = NULL,
      $sequence         = array(),
      $sequenceReverse  = array();      

    /**
     * Set Last_id
     *
     * @access  public
     * @param   int last_id
     */
    function setLast_id($last_id) {
      $this->last_id= $last_id;
    }

    /**
     * Set Current_id
     *
     * @access  public
     * @param   &lang.Object current_id
     */
    function setCurrent_id(&$current_id) {
      $this->current_id= &$current_id;
    }

    /**
     * Get Current_id
     *
     * @access  public
     * @return  &lang.Object
     */
    function &getCurrent_id() {
      return $this->current_id;
    }

    /**
     * Get Last_id
     *
     * @access  public
     * @return  int
     */
    function getLast_id() {
      return $this->last_id;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function hasId($id) {
      return isset($this->sequence[$id]);
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function dateExists($date) {
      return isset($this->sequenceReverse[$date]);
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function idFor($date) {
      if (isset($this->sequenceReverse[$date])) return $this->sequenceReverse[$date];
      return throw(new ElementNotFoundException('No shot for date '.$date));
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function dateFor($id) {
      if (isset($this->sequence[$id])) return $this->sequence[$id];
      return throw(new ElementNotFoundException('No such id: '.$id));
    }    
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function getPredecessorDate($date) {
      if (!$this->dateExists($date)) return FALSE;
      
      end($this->sequence);
      while (current($this->sequence) != $date) prev($this->sequence);
      prev($this->sequence);
      return current($this->sequence);
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function getSuccessorDate($date) {
      if (!$this->dateExists($date)) return FALSE;
      
      end($this->sequence);
      
      // Bordercase: given date is the last one
      if (current($this->sequence) == $date) return FALSE;
      
      while (current($this->sequence) != $date) prev($this->sequence);
      next($this->sequence);
      return current($this->sequence);
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function getLatestDate() {
      return $this->dateFor($this->getCurrent_id());
    }    
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function addShot($id, $date) {
      if ($this->dateExists($date) || $this->hasId($id)) {
        return throw(new IllegalStateException(
          'Shot '.$id.' or date '.$date.' already registered in this album.'
        ));
      }
      
      if (3 != sscanf($date, '%4d/%2d/%2d', $year, $month, $day)) {
        return throw(new IllegalArgumentException(
          'Invalid date format, format must be yyyy/mm/dd.'
        ));
      }

      $this->sequence[$id]= $date;
      $this->sequenceReverse[$date]= $id;
    }
  }
?>
