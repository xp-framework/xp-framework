<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Workflow
   *
   * Note: To produce a significantly lower memory footprint,
   * only the state's names are stored in the member variable
   * <tt>states</tt>.
   *
   * @see      xp://org.apache.xml.workflow.StateFlowManager
   * @purpose  A single workflow
   */
  class Workflow extends Object {
    var
      $states   = array(),
      $offset   = 0;
    
    /**
     * Returns index of state name
     *
     * @access  public
     * @param   string name
     * @return  int offset or FALSE if not existant
     */
    function indexOf($name) {
      return array_search($name, $this->states, TRUE);
    }
    
    /**
     * Prepend a state
     *
     * @access  public
     * @param   string name
     * @return  bool
     */
    function prepend($name) {
      if (FALSE !== $this->indexOf($name)) return FALSE;
      return (bool)array_unshift($this->states, $name);
    }

    /**
     * Append a state
     *
     * @access  public
     * @param   string name
     * @return  bool
     */
    function append($name) {
      if (FALSE !== $this->indexOf($name)) return FALSE;
      return (bool)array_push($this->states, $name);
    }
    
    /**
     * Insert state before a specified state
     *
     * @access  public
     * @param   string before
     * @param   string name
     * @return  bool
     */
    function insertBefore($before, $name) {
      if (FALSE !== $this->indexOf($name)) return FALSE;
      
      $offset= $this->indexOf($before);
      $this->states= array_merge(
        array_slice($this->states, 0, $offset), 
        $name,
        array_slice($this->states, $offset)
      );
      
      return TRUE;
    }

    /**
     * Insert state after a specified state
     *
     * @access  public
     * @param   string after
     * @param   string name
     * @return  bool
     */
    function insertAfter($after, $name) {
      if (FALSE !== $this->indexOf($name)) return FALSE;
      
      $offset= $this->indexOf($after);
      $this->states= array_merge(
        array_slice($this->states, 0, $offset+ 1), 
        $name,
        array_slice($this->states, $offset+ 1)
      );
      
      return TRUE;
    }
    
    /**
     * Sets states
     *
     * @access  public
     * @param   string[] states
     */
    function setStates($states) {
      $this->states= $states;
    }

    /**
     * Get state at specified offset
     *
     * @access  public
     * @param   int offset
     * @return  string
     */
    function stateAt($offset) {
      return $this->states[$offset];
    }

    /**
     * Get first state
     *
     * @access  public
     * @return  string
     */
    function getFirstState() {
      return $this->states[0];
    }
        
    /**
     * Get next state
     *
     * @access  public
     * @return  string
     */
    function getNextState() {
      return $this->states[min(sizeof($this->states)- 1, $this->offset+ 1)];
    }
    
    /**
     * Get current state
     *
     * @access  public
     * @return  string
     */
    function getCurrentState() {
      return $this->states[$this->offset];
    }
    
    /**
     * Get previous state
     *
     * @access  public
     * @return  string
     */
    function getPreviousState() {
      return $this->states[max($this->offset - 1, 0)];
    }
    
    /**
     * Set current state
     *
     * @access  public
     * @param   string name
     */
    function setCurrentState($name) {
      $this->offset= array_search($name, $this->states);
    }
  }
?>
