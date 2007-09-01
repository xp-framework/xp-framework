<?php
/* This class is part of the XP framework
 *
 * $Id: UskaScriptlet.class.php 8975 2006-12-27 18:06:40Z friebe $ 
 */

  namespace de::uska::scriptlet;

  ::uses(
    'scriptlet.xml.workflow.AbstractXMLScriptlet',
    'xml.DomXSLProcessor'
  );

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class UskaScriptlet extends scriptlet::xml::workflow::AbstractXMLScriptlet {
  
    /**
     * Set our own processor object
     *
     * @return  &xml.XSLProcessor
     */
    protected function _processor() {
      return new xml::DomXSLProcessor();
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
    
    /**
     * Decide whether a context is needed. Whenever a session is required
     * we also need a context.
     *
     * @param   &scriptlet.xml.workflow.WorkflowScriptletRequest request
     * @return  bool
     */
    public function wantsContext($request) {
      return $this->needsSession($request) || $request->hasSession();
    }
  }
?>
