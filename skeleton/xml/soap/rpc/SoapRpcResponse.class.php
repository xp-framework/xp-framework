<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('scriptlet.HttpScriptletResponse');
  
  /**
   * Wraps SOAP response
   *
   * @see scriptlet.HttpScriptletResponse  
   */
  class SoapRpcResponse extends HttpScriptletResponse {
    var 
      $message= NULL;
    
    /**
     * Constructor
     *
     * @access  public
     */
    function __construct() {
      $this->setHeader('Server', 'SOAP 1.0#/PHP'.phpversion().'/'.php_uname());
    }
    
    /**
     * Sets SOAP message
     *
     * @access  public
     * @param   &xml.soap.SOAPMessage msg SOAPmessage object
     */
    function setMessage(&$msg) {
      $this->message= &$msg;
    }
    
    /**
     * Make sure a fault is passed as "500 Internal Server Error"
     *
     * @access  public
     * @see     scriptlet.HttpScriptletResponse#process
     */
    function process() {
      if (!$this->message) return;

      $this->setHeader('Content-type', 'text/xml');      
      if (NULL !== $this->message->getFault()) {
        $this->setStatus(HTTP_INTERNAL_SERVER_ERROR);
      }
      $this->content= (
        $this->message->getDeclaration()."\n".
        $this->message->getSource(0)
      );
    }
  }
?>
