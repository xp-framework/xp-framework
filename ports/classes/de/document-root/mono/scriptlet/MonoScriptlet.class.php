<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('scriptlet.xml.workflow.AbstractXMLScriptlet', 'xml.DomXSLProcessor');

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class MonoScriptlet extends AbstractXMLScriptlet {

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function &_processor() {
      $p= &new DomXSLProcessor();
      return $p;
    }
      
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function _setStylesheet(&$request, &$response) {
      $response->setStylesheet(sprintf(
        '%s/%s.xsl',
        $request->getProduct(),
        $request->getStateName()
      ));
    }  
  }
?>
