<?php
/* This class is part of the XP framework
 *
 * $Id: InteropScriptlet.class.php 8974 2006-12-27 17:29:09Z friebe $ 
 */

  namespace net::xp_framework::scriptlet::interop;

  ::uses(
    'scriptlet.xml.workflow.AbstractXMLScriptlet',
    'xml.DomXSLProcessor'
  );

  /**
   * Website scriptlet for http://interop.xp-framework.net/
   *
   * @see      http://interop.xp-framework.net/
   * @purpose  Scriptlet
   */
  class InteropScriptlet extends scriptlet::xml::workflow::AbstractXMLScriptlet {

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
