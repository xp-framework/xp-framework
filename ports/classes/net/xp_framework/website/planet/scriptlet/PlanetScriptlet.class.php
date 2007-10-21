<?php
/* This class is part of the XP framework
 *
 * $Id: WebsiteScriptlet.class.php 8974 2006-12-27 17:29:09Z friebe $ 
 */

  uses(
    'scriptlet.xml.workflow.AbstractXMLScriptlet',
    'xml.DomXSLProcessor'
  );

  /**
   * Website scriptlet for http://xp-framework.info/
   *
   * @see      http://xp-framework.info/
   * @purpose  Scriptlet
   */
  class PlanetScriptlet extends AbstractXMLScriptlet {

    /**
     * Set our own processor object
     *
     * @return  xml.XSLProcessor
     */
    protected function _processor() {
      return new DomXSLProcessor();
    }
    
    /**
     * Sets the responses XSL stylesheet
     *
     * @param   scriptlet.xml.XMLScriptletRequest request
     * @param   scriptlet.xml.XMLScriptletResponse response
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
