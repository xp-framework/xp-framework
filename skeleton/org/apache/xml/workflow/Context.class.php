<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'org.apache.xml.workflow.ContextResourceManager',
    'org.apache.xml.workflow.State'
  );

  /**
   * (Insert class' description here)
   *
   * @ext      extensiom
   * @see      reference
   * @purpose  purpose
   */
  class Context extends Object {
    var
      $classloader  = NULL,
      $crm          = NULL;
      
    /**
     * Called to initialize this application context
     *
     * @access  public
     * @param   &lang.ClassLoader classloader
     */
    function initialize(&$classloader) {
      $this->classloader= &$classloader;
      $this->crm= &new ContextResourceManager();
      $this->crm->initialize();
    }
    
    /**
     * Handle a single request
     *
     * @access  public
     * @param   &org.apache.xml.XMLScriptletRequest request
     * @param   &org.apache.xml.XMLScriptletResponse response
     * @return  bool
     */
    function handleRequest(&$request, &$response) {
    
      // FIXME for debugging only - remove me! (and all calls to $cat->)
      $l= &Logger::getInstance();
      $cat= &$l->getCategory($this->getClassName());
      
      // Load corresponding state
      // TBD: StateFlows
      do {
        if (!($state= &$this->_getCorrespondingState($request->getState()))) return FALSE;
        
        $accessible= $state->isAccessible();
        $cat->info($state, $accessible);
      } while (!$accessible);
      
      return TRUE;
    }
    
    /**
     * Returns corresponding state
     *
     * @access  private
     * @param   string name
     * @return  &org.apache.xml.workflow.State
     */
    function &_getCorrespondingState($name) {
      try(); {
        $class= &$this->classloader->loadClass(ucfirst($name).'State');
      } if (catch('ClassNotFoundException', $e)) {
        return throw(new HttpScriptletException($e->message));
      } if (catch('RunTimeException', $e)) {
        return throw(new HttpScriptletException($e->message));
      }
      
      return new $class();
    }
  }
?>
