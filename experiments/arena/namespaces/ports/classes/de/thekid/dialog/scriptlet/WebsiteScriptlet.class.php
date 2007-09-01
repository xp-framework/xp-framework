<?php
/* This class is part of the XP framework's port "Dialog"
 *
 * $Id: WebsiteScriptlet.class.php 8974 2006-12-27 17:29:09Z friebe $ 
 */

  namespace de::thekid::dialog::scriptlet;

  ::uses(
    'scriptlet.xml.workflow.AbstractXMLScriptlet',
    'xml.DomXSLProcessor'
  );

  /**
   * Website scriptlet for the Album port
   *
   * @see      http://album.friebes.info/
   * @purpose  Scriptlet
   */
  class WebsiteScriptlet extends scriptlet::xml::workflow::AbstractXMLScriptlet {

    /**
     * Set our own processor object
     *
     * @return  &.xml.XSLProcessor
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
  }
?>
