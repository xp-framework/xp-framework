<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('org.apache.xml.workflow.Handler');
  
  /**
   * (Insert class' description here)
   *
   * @ext      extensiom
   * @see      reference
   * @purpose  Base class
   */
  class State extends Object {
    var
      $name     = '',
      $handlers = array();

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function &addHandler(&$handler) {
      $this->handlers[]= &$handler;
      return $handler;
    }

    /**
     * Set Name
     *
     * @access  public
     * @param   string name
     */
    function setName($name) {
      $this->name= $name;
    }

    /**
     * Get Name
     *
     * @access  public
     * @return  string
     */
    function getName() {
      return $this->name;
    }

    /**
     * Return whether this state should be accessible. This is 
     * TRUE for a situation in which no handlers exist or for
     * the situation in which at least _one_ handler's prerequisites
     * are met.
     *
     * @see     xp://org.apache.xml.workflow.Handler#prerequisitesMet
     * @access  
     * @param   
     * @return  
     */
    function isAccessible(&$context) {
      for ($i= 0, $s= sizeof($this->handlers); $i < $s; $i++) {
        if ($this->handlers[$i]->prerequisitesMet($context)) return TRUE;
      }
      return ($s == 0);
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function isSubmitTrigger(&$request) {
      return ($request->hasParam('__form') || $request->hasParam('__sendingdata'));
    }
    
    /**
     * 
     *
     * @access  
     * @param   
     * @param   
     * @param   
     * @return  bool
     */
    function getDocument(&$context, &$request, &$response) {
    }
  }
?>
