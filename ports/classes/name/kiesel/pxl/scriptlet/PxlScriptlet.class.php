<?php
/* This class is part of the XP framework's port "Dialog"
 *
 * $Id: WebsiteScriptlet.class.php 4691 2005-02-20 00:44:47Z friebe $ 
 */

  uses(
    'scriptlet.xml.workflow.AbstractXMLScriptlet', 
    'xml.DomXSLProcessor'
  );

  /**
   * Website scriptlet for the Album port
   *
   * @see      http://pxl.kiesel.name/
   * @purpose  Scriptlet
   */
  class PxlScriptlet extends AbstractXMLScriptlet {

    /**
     * Set our own processor object
     *
     * @access  protected
     * @return  &.xml.XSLProcessor
     */
    function &_processor() {
      $p= &new DomXSLProcessor();
      return $p;
    }
    
    /**
     * Returns whether we need a context
     *
     * @access  
     * @param   
     * @return  
     */
    function wantsContext(&$request) {
      return $this->needsSession($request) || $request->hasSession();
    }

    /**
     * Sets the responses XSL stylesheet
     *
     * @access  protected
     * @param   &scriptlet.scriptlet.XMLScriptletRequest request
     * @param   &scriptlet.scriptlet.XMLScriptletResponse response
     */
    function _setStylesheet(&$request, &$response) {
      $response->setStylesheet(sprintf(
        '%s/%s.xsl',
        $request->getProduct(),
        $request->getStateName()
      ));
    }
  }
?>
