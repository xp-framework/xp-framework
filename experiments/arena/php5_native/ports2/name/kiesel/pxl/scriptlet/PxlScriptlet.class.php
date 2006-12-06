<?php
/* This class is part of the XP framework's port "Dialog"
 *
 * $Id$ 
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
    public function &_processor() {
      $p= new DomXSLProcessor();
      return $p;
    }
    
    /**
     * Returns whether we need a context
     *
     * @access  
     * @param   
     * @return  
     */
    public function wantsContext(&$request) {
      return $this->needsSession($request) || $request->hasSession();
    }

    /**
     * Sets the responses XSL stylesheet
     *
     * @access  protected
     * @param   &scriptlet.scriptlet.XMLScriptletRequest request
     * @param   &scriptlet.scriptlet.XMLScriptletResponse response
     */
    public function _setStylesheet(&$request, &$response) {
      $response->setStylesheet(sprintf(
        '%s/%s.xsl',
        $request->getProduct(),
        $request->getStateName()
      ));
    }
  }
?>
