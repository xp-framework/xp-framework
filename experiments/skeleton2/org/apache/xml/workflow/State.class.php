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
    public
      $name     = '',
      $handlers = array();
      
    /**
     * Retrieve instance of this state
     *
     * @access  public
     * @param   string name
     * @return  &org.apache.xml.workflow.State
     */
    public function getInstance($name) {
      static $instance= array();
      
      if (!isset($instance[$name])) {
        $class= $name.'State';
        $instance[$name]= XPClass::forName($class)->newInstance();
        $instance[$name]->name= $name;
      }
      
      return $instance[$name];
    }
    
    /**
     * Initialize this state
     *
     * @model   abstract
     * @access  public
     * @param   &org.apache.xml.workflow.Context context
     */  
    public abstract function initialize(&$context) ;

    /**
     * Add a handler
     *
     * @access  public
     * @param   &org.apache.xml.workflow.Handler handler
     * @return  &org.apache.xml.workflow.Handler handler
     */
    public function addHandler(&$handler) {
      $this->handlers[]= $handler;
      return $handler;
    }

    /**
     * Set this state's name
     *
     * @access  public
     * @param   string name
     */
    public function setName($name) {
      $this->name= $name;
    }

    /**
     * Get this state's name
     *
     * @access  public
     * @return  string
     */
    public function getName() {
      return $this->name;
    }

    /**
     * Return whether this state should be accessible.
     *
     * @access  public
     * @param   &org.apache.xml.workflow.Context context
     * @param   &org.apache.xml.XMLScriptletRequest request
     * @return  bool
     */
    public function isAccessible(&$context, &$request) {
      $l= Logger::getInstance();
      $cat= $l->getCategory(self::getClassName());

      if (0 == ($s= sizeof($this->handlers))) {     // Border case
        $cat->warn(self::getClassName(), 'accessible');
        return TRUE;
      }
      
      for ($i= 0; $i < $s; $i++) {
        $cat->infof('Calling handler #%d: %s', $i, $this->handlers[$i]->getClassName());
                
        if (!$this->handlers[$i]->prerequisitesMet($context)) {
          $cat->warn($i, '>> Prerequisites not met, page is not accessible');
          
          // Here we know at least one handler's prerequisites are 
          // *not* met - no need to check the rest:)
          return FALSE;
        }
        
        if ($this->handlers[$i]->isActive($context)) {
          $cat->warn($i, '>> is active, page is accessible');
          
          // Here we know at least one handler is active - therefore
          // we can safely assume it's OK to show the page.
          return TRUE;
        }
      }
      
      $cat->warn(self::getClassName(), 'not accessible');
      return FALSE;
    }
    
    /**
     * Returns whether this state has been triggered by submit data
     *
     * @access  public
     * @param   &org.apache.xml.workflow.Context context
     * @param   &org.apache.xml.XMLScriptletRequest request
     * @return  string submit trigger's name or NULL
     */
    public function isSubmitTrigger(&$context, &$request) {
      return $request->hasParam('__form');
    }

    /**
     * Returns whether this state has been triggered directly
     *
     * @access  public
     * @param   &org.apache.xml.workflow.Context context
     * @param   &org.apache.xml.XMLScriptletRequest request
     * @return  string submit trigger's name or NULL
     */
    public function isDirectTrigger(&$context, &$request) {
      return $request->hasParam('__sendingdata');
    }
    
    /**
     * Retrieve result document
     *
     * @access  public
     * @param   &org.apache.xml.workflow.Context context
     * @param   &org.apache.xml.XMLScriptletRequest request
     * @param   &org.apache.xml.XMLScriptletResponse response
     * @return  bool
     */
    public function getDocument(&$context, &$request, &$response) {
      $l= Logger::getInstance();
      $cat= $l->getCategory(self::getClassName());
      
      $s= sizeof($this->handlers);
      $return= TRUE;

      // If we have a submit trigger:
      if (self::isSubmitTrigger($context, $request)) {
        for ($i= 0; $i < $s; $i++) {
          $cat->infof('Calling handler #%d: %s', $i, $this->handlers[$i]->getClassName());
          
          if (!$this->handlers[$i]->isActive($context)) {
            $cat->warn($i, 'isActive returns FALSE, proceeding...');
            
            // The handler is not active - thus, proceed to the next handler
            continue;
          }
          
          $cat->warn($i, 'Handling submitted data...');
          if (!$this->handlers[$i]->handleSubmittedData($context, $request)) {
            $cat->error('Errors occured', $this->handlers[$i]->errors);
            
            // The handler states there were errors. Add these to the
            // formerror document node
            foreach ($this->handlers[$i]->errors as $error) {
              $response->addFormError(
                $this->handlers[$i]->getClassName(),
                $error[1],
                $error[0]
              );
            }
            
            // At least one handler has generated errors, we can not continue
            $return= FALSE;
            continue;
          }
          
          // 
        }
      }
      
      // Go through all existing context resources and call their "insertStatus" method
      foreach (array_keys($context->crm->hash) as $name) {
        if (!isset($context->crm->crs[$context->crm->hash[$name]])) continue;

        $crs= $context->crm->crs[$context->crm->hash[$name]];
        $cat->debug('Calling insertStatus() for', get_class($crs), $name);
        $crs->insertStatus($response->addFormResult(new Node(
          'cr', 
          NULL, 
          array('name' => $crs->getClassName())
        )));
      }
      
      return $return;
    }
  }
?>
