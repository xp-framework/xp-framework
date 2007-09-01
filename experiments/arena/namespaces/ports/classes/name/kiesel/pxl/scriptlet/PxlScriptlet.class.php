<?php
/* This class is part of the XP framework's port "Dialog"
 *
 * $Id: WebsiteScriptlet.class.php 4691 2005-02-20 00:44:47Z friebe $ 
 */

  namespace name::kiesel::pxl::scriptlet;

  ::uses(
    'scriptlet.xml.workflow.AbstractXMLScriptlet',
    'xml.DomXSLProcessor'
  );

  /**
   * Website scriptlet for the Album port
   *
   * @see      http://pxl.kiesel.name/
   * @purpose  Scriptlet
   */
  class PxlScriptlet extends scriptlet::xml::workflow::AbstractXMLScriptlet {

    /**
     * Set our own processor object
     *
     * @return  &.xml.XSLProcessor
     */
    protected function _processor() {
      $p= new xml::DomXSLProcessor();
      return $p;
    }
    
    /**
     * Returns whether we need a context
     *
     * @param   
     * @return  
     */
    public function wantsContext($request) {
      return $this->needsSession($request) || $request->hasSession();
    }

    /**
     * Sets the responses XSL stylesheet
     *
     * @param   &scriptlet.scriptlet.XMLScriptletRequest request
     * @param   &scriptlet.scriptlet.XMLScriptletResponse response
     */
    protected function _setStylesheet($request, $response) {
      $response->setStylesheet(sprintf(
        '%s/%s.xsl',
        $request->getProduct(),
        $request->getStateName()
      ));
    }
  }
?>
