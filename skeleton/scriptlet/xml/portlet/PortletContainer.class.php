<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('scriptlet.xml.portlet.Portlet');

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class PortletContainer extends Object {
    var
      $portlets= array();

    /**
     * Add Portlets
     *
     * @access  public
     * @param   mixed[] portlets
     */
    function addPortlet($name, $classname) {
      try(); {
        $class= &XPClass::forName($classname);
      } if (catch('ClassNotFoundException', $e)) {
        return throw($e);
      }

      $this->portlets[]= &$class->newInstance($name);
    }

    /**
     * Process container
     *
     * @access  public
     * @param   &scriptlet.xml.workflow.WorkflowScriptletRequest request 
     * @param   &scriptlet.xml.XMLScriptletResponse response 
     * @param   &scriptlet.xml.Context context
     */
    function process(&$request, &$response, &$context) {
      $rundata= &new Rundata();
      $rundata->request= &$request;
      $rundata->context= &$context;

      $node= &$response->addFormResult(new Node('portlets'));

      for ($i= 0, $s= sizeof($this->portlets); $i < $s; $i++) {
        $portlet= &$node->addChild(new Node('portlet', NULL, array(
          'name'  => $this->portlets[$i]->getName()
        )));
        
        try(); {
          $content= &$this->portlets[$i]->getContent($rundata);
        } if (catch('Throwable', $e)) {
          $response->addFormError($e->getClassName(), '*', $e->getMessage());
          return;
        }
        
        
        $portlet->addChild($content);
      }
    }  
  }
?>
