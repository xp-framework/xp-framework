<?php
/* This class is part of the XP framework
 *
 * $Id$ 
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
  class WebsiteScriptlet extends AbstractXMLScriptlet {

    /**
     * Set our own processor object
     *
     * @return  &.xml.XSLProcessor
     */
    protected function _processor() {
      return new DomXSLProcessor();
    }
  }
?>
