<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * "DOM" XSL processor 
   *
   * @ext      dom
   * @purpose  XSL processor
   */
  class DomXSLProcessor extends XSLProcessor {
  
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    public function setParameter($key, $val) {
    }

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    public function setParameters($p) {
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    public function setErrorListener(&$listener) {
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    public function setSchemeHandler($scheme, &$handler) {
    }
  
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    public function process(&$source, &$stylesheet) {
      if (!($r= $stylesheet->dom->process($source->dom, $this->params))) {
        throw (new TransformerException('...'));
      }
      
      return new DomXSLResult($r);
    }
  }
?>
