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
     * Return whether this state should be accessible. This is 
     * TRUE for a situation in which no handlers exist or for
     * the situation in which at least _one_ handler's prerequisites
     * are met.
     *
     * @see     xp://org.apache.xml.workflow.Handler#prerequisitesMet
     * @access  public
     * @param   &org.apache.xml.workflow.Context context
     * @return  bool
     */
    function isAccessible(&$context) {
      for ($i= 0, $s= sizeof($this->handlers); $i < $s; $i++) {
        if ($this->handlers[$i]->prerequisitesMet($context)) return TRUE;
      }
      return ($s == 0);
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
