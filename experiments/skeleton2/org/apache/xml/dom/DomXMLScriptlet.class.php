<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'org.apache.xml.XMLScriptlet',
    'org.apache.xml.dom.DomXMLScriptletResponse'
  );

  /**
   * Specialized version of the XMLScriptlet that uses DomXML / DomXSL
   * instead of Sablotron to transform
   *
   * @ext      domxml
   * @see      xp://org.apache.xml.XMLSriptlet
   * @purpose  XMLScriptlet using DomXML
   */
  class DomXMLScriptlet extends XMLScriptlet {

    /**
     * Set our own response object
     *
     * @access  protected
     * @see     xp://org.apache.XMLScriptlet#_response
     */
    protected function _response() {
      $this->response= new DomXMLScriptletResponse();
    }
  }
?>
