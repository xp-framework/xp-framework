<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('org.apache.xml.workflow.Handler');
  
  /**
   * State
   *
   * @see      xp://org.apache.xml.workflow.AbstractXMLScriptlet
   * @purpose  Base class
   */
  class State extends Object {
    var
      $name     = '',
      $handlers = array();
    
    /**
     * Initialize this state
     *
     * @model   abstract
     * @access  public
     * @param   &org.apache.xml.workflow.Context context
     */  
    function initialize(&$context) { }

    /**
     * Add a handler
     *
     * @access  public
     * @param   &org.apache.xml.workflow.Handler handler
     * @return  &org.apache.xml.workflow.Handler handler
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
     * Return whether this state should be accessible.
     *
     * @access  public
     * @param   &org.apache.xml.workflow.Context context
     * @return  bool
     */
    function isAccessible(&$context) {
      return TRUE;
    }
    
    /**
     * Returns whether this state has been triggered by submit data
     *
     * @access  public
     * @param   &org.apache.xml.HttpScriptletRequest request
     * @return  string submit trigger's name or NULL
     */
    function isSubmitTrigger(&$request) {
      foreach (array('form', 'sendingdata') as $magic) {
        if ($request->hasParam('__'.$magic)) return $request->getParam('__'.$magic);
      }
      return NULL;
    }
    
    /**
     * Include your application logic here
     *
     * @access  public
     * @param   &org.apache.xml.workflow.Context context
     * @param   &org.apache.xml.HttpScriptletRequest request
     * @param   &org.apache.xml.HttpScriptletResponse response
     * @return  bool
     */
    function getDocument(&$context, &$request, &$response) {
    }
  }
?>
