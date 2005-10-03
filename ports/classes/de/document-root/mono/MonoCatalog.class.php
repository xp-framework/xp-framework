<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('lang.ElementNotFoundException');

  /**
   * Represent a catalog of MonoPictures and their
   * associated dates and ids.
   *
   * @purpose  Catalog
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
     * Checks whether a shot for the id exists.
     *
     * @access  public
     * @param   int it
     * @return  bool
     */
    function hasId($id) {
      return isset($this->sequence[$id]);
    }
    
    /**
     * Checks whether a shot for the date exists.
     *
     * @access  public
     * @param   string date
     * @return  bool
     */
    function dateExists($date) {
      return isset($this->sequenceReverse[$date]);
    }
    
    /**
     * Get id for date.
     *
     * @access  public
     * @param   string date
     * @return  int id
     * @throws  lang.ElementNotFoundException if no such date exists
     */
    function idFor($date) {
      if (isset($this->sequenceReverse[$date])) return $this->sequenceReverse[$date];
      return throw(new ElementNotFoundException('No shot for date '.$date));
    }
    
    /**
     * Get date for id.
     *
     * @access  public
     * @param   int id
     * @return  string date
     * @throws  lang.ElementNotFoundException if no such date exists
     */
    function dateFor($id) {
      if (isset($this->sequence[$id])) return $this->sequence[$id];
      return throw(new ElementNotFoundException('No such id: '.$id));
    }    
    
    /**
     * Get previous date for the date
     *
     * @access  public
     * @param   string date
     * @return  string date
     */
    function getPredecessorDate($date) {
      if (!$this->dateExists($date)) return FALSE;
      
      end($this->sequence);
      while (current($this->sequence) != $date) prev($this->sequence);
      prev($this->sequence);
      return current($this->sequence);
    }
    
    /**
     * Get next date for the date
     *
     * @access  public
     * @param   string date
     * @return  string date
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
     * Get latest date.
     *
     * @access  public
     * @return  string date
     */
    function getLatestDate() {
      return $this->dateFor($this->getCurrent_id());
    }    
    
    /**
     * Add a new shot to the catalog for a specified date.
     *
     * @access  public
     * @param   int id
     * @param   string date
     * @throws  lang.IllegalStateException if date or id already used
     * @throws  lang.IllegalArgumentException if date is invalid
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
