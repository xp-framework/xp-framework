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
      $this->setHeader('Content-type', 'text/xml');
    }
    
    /**
     * Overwritten method from parent class
     *
     * @access  public
     * @throws  IllegalAccessException
     * @see     #setMessage
     */
    function write() {
      throw(new IllegalAccessException('Cannot write directly'));
    }
    
    /**
     * Overwritten method from parent class
     *
     * @access  public
     * @throws  IllegalAccessException
     * @see     #setMessage
     */
    function setContent() {
      throw(new IllegalAccessException('Cannot write directly'));
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
     * Gets content for HTTP response
     *
     * @access  public
     * @return  string content
     * @see     scriptlet.HttpScriptletResponse#getContent
     */
    function getContent() {
      return (
        $this->message->getDeclaration()."\n".
        $this->message->getSource(0)
      );
    }
    
    /**
     * Make sure a fault is passed as "500 Internal Server Error"
     *
     * @access  public
     * @see     scriptlet.HttpScriptletResponse#process
     */
    function process() {
      if ($this->message && NULL !== $this->message->getFault()) {
        $this->setStatus(HTTP_INTERNAL_SERVER_ERROR);
      }
    }
    
    /**
     * Destructor
     *
     * @access  public
     */
    function __destruct() {
      if (is_a($this->message, 'Object')) $this->message->__destruct();
      parent::__destruct();
    }
  }
?>
