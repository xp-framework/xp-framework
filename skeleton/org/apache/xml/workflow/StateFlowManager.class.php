<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * (Insert class' description here)
   *
   * @ext      extensiom
   * @see      reference
   * @purpose  purpose
   */
  class StateFlowManager extends Object {
    var
      $flow     = array(),
      $offset   = 0;
      
    /**
     * Called to initialize this stateflow manager
     *
     * @access  public
     * @param   &lang.ClassLoader classloader
     */
    function initialize(&$classloader) {
      $this->classloader= &$classloader;
      $this->offset= 0;
    }

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function getFirstState() {
      return $this->getStateByName($this->flow[0]);
    }
        
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function getNextState() {
      return $this->getStateByName(@$this->flow[$this->offset + 1]);
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function getCurrentState() {
      return $this->getStateByName(@$this->flow[$this->offset]);
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function getPreviousState() {
      return $this->getStateByName(@$this->flow[$this->offset - 1]);
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function setCurrentState(&$state) {
      $this->offset= array_search($state->getName(), $this->flow);
    }
    
    /**
     * Returns corresponding state
     *
     * @access  private
     * @param   string name
     * @return  &org.apache.xml.workflow.State
     */
    function &getStateByName($name) {
      if (NULL === $name) return NULL;
      
      try(); {
        $class= &$this->classloader->loadClass(ucfirst($name).'State');
      } if (catch('ClassNotFoundException', $e)) {
        return throw(new HttpScriptletException($e->message));
      } if (catch('RunTimeException', $e)) {
        return throw(new HttpScriptletException($e->message));
      }
      
      $state= &new $class();
      $state->setName($name);
      
      return $state;
    }
  }
?>
