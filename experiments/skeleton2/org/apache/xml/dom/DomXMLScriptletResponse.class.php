<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'org.apache.xml.XMLScriptletResponse',
    'xml.DomXSLProcessor'
  );

  /**
   * Wraps XML response for Dom
   *
   * @purpose  DomXML
   */
  class DomXMLScriptletResponse extends XMLScriptletResponse {
    
    /**
     * Retrieve the XSL processor
     *
     * @access  protected
     * @return  &xml.XSLProcessor
     */
    protected function getProcessor() {
      return new DomXSLProcessor();
    }
  }
?>
