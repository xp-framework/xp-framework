<?php
/* Diese Klasse ist Teil des XP-Frameworks
 *
 * $Id$
 */

  /**
   * Kapselt den HTTP-Request des HTTP-Moduls
   *
   * @see org.apache.HTTPModule
   */  
  class HTTPModuleRequest extends Object {
    var
      $headers=         array(),
      $params=          array(),
      $data=            '';
      
    var
      $method;

    /**
     * Gibt eine Umgebungsvariable zurücke
     *
     * @access  public
     * @param   string name Header
     * @return  string Header-Wert
     */
    function getEnvValue($name) {
      return getenv($name);
    }
      
    /**
     * Gibt einen Request-Header zurück
     *
     * @access  public
     * @param   string name Header
     * @return  string Header-Wert
     */
    function getHeader($name) {
      return $this->headers[$name];
    }
    
    /**
     * Gibt eine Request-Variable zurück
     *
     * @access  public
     * @param   string name Header
     * @return  string Header-Wert
     */
    function getParam($name) {
      return $this->params[$name];
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function setParams(&$params) {
      $this->params= &$params;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function setData(&$data) {
      $this->data= &$data;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function &getData() {
      return $this->data;
    }
  }
?>
