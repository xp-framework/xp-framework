<?php
/* This class is part of the XP framework
 *
 * $Id: WebsiteScriptlet.class.php 8974 2006-12-27 17:29:09Z friebe $ 
 */

  namespace net::xp_framework::scriptlet;

  ::uses(
    'scriptlet.xml.workflow.AbstractXMLScriptlet',
    'xml.DomXSLProcessor'
  );

  /**
   * Website scriptlet for http://xp-framework.info/
   *
   * @see      http://xp-framework.info/
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
  }
?>
