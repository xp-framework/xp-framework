<?php
/* Diese Klasse ist Teil des XP-Frameworks
 *
 * $Id$
 */

  /**
   * Kapselt den HTTP-Response des HTTP-Moduls
   *
   * @see org.apache.HTTPModule
   */  
  class HTTPModuleResponse extends Object {
    var
      $content=         '',
      $statusCode=      200,
      $headers=         array();
    
    /**
     * Fügt dem Response einen Header hinzu
     *
     * @access  public
     * @param   string name Header
     * @param   string value Header-Wert
     */
    function addHeader($name, $value) {
      $this->headers[$name]= $value;
    }
    
    /**
     * Diese Methode schickt die HTTP-Response-Header
     *
     * @access  public
     */  
    function sendHeaders() {

      // Statuscode senden
      header('HTTP/1.1 '.$this->statusCode);
      
      // Weitere Header
      foreach ($this->headers as $key=> $val) {
        header($key.': '.$val);
      }
    }
    
    /**
     * Gibt den Seiten-Content zurück
     *
     * @access  public
     * @return  string Content
     */
    function getContent() {
      return $this->content;
    }
  }
?>
