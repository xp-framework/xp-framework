<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * XSL processor
   *
   * <code>
   *   $proc= new XSLProcessor();
   *   try(); {
   *     $result= $proc->transform(
   *       Source::fromString('...'), 
   *       StyleSheet::fromFile('foo.xsl')
   *     );
   *  } if (catch('TransformerException', $e)) {
   *    $e->printStackTrace();
   *    exit(-1);
   *  }
   *  var_dump($result->toString());
   * </code>
   *
   * @purpose  Base class
   */
  class XSLProcessor extends Object {

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    public function setParameter($key, $val) { }

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    public function setParameters($p) { }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    public function setErrorListener(&$listener) { }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    public function setSchemeHandler($scheme, &$handler) { }
  
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    public function process(&$source, &$stylesheet) { }
  
  }
?>
