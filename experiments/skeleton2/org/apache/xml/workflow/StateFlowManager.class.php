<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('org.apache.xml.workflow.Workflow');

  /**
   * Stateflow
   *
   * @see      xp://org.apache.xml.workflow.Context
   * @purpose  Controls state flows
   */
  class StateFlowManager extends Object {
    public
      $flows    = array(),
      $current  = NULL;
      
    /**
     * Called to initialize this stateflow manager
     *
     * @access  public
     */
    public function initialize() {
      self::setCurrentFlow(NULL);
    }
    
    /**
     * Set current flow. Creates workflow if necessary
     *
     * @access  public
     * @param   string name
     * @return  &org.apache.xml.workflow.Workflow
     */
    public function setCurrentFlow($name) {
      $this->current= $name;
      if (NULL === $name) return NULL;      // No flow...
      
      if (!isset($this->flows[$name])) {
        $this->flows[$name]= new Workflow();
      }
      return $this->flows[$name];
    }
    
    /**
     * Get current flow
     *
     * @access  public
     * @return  &org.apache.xml.workflow.Workflow
     */
    public function getCurrentFlow() {
      if (isset($this->flows[$this->current])) {
        return $this->flows[$this->current];
      } else {
        return NULL;
      }
    }
  }
?>
