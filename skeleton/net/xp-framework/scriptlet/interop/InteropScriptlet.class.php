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
   * Website scriptlet for http://interop.xp-framework.net/
   *
   * @see      http://interop.xp-framework.net/
   * @purpose  Scriptlet
   */
  class InteropScriptlet extends AbstractXMLScriptlet {

    /**
     * Set our own processor object
     *
     * @access  protected
     * @return  &.xml.XSLProcessor
     */
    function &_processor() {
      return new DomXSLProcessor();
    }
  }
?>
