<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses(
    'org.apache.HttpScriptletResponse'
  );
  
  /**
   * Wraps SOAP response
   *
   * @see org.apache.HttpScriptletResponse  
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
      parent::__construct();
    }
    
    /**
     * Overwritten method from parent class
     *
     * @access  public
     * @throws  IllegalAccessException
     * @see     #setMessage
     */
    function write() {
      throw(IllegalAccessException('Cannot write directly'));
    }
    
    /**
     * Overwritten method from parent class
     *
     * @access  public
     * @throws  IllegalAccessException
     * @see     #setMessage
     */
    function setContent() {
      throw(IllegalAccessException('Cannot write directly'));
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
     * @see     org.apache.HttpScriptletResponse#getContent
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
     * @see     org.apache.HttpScriptletResponse#process
     */
    function process() {
      if (NULL !== $this->message->getFault()) {
        $this->setStatus(HTTP_INTERNAL_SERVER_ERROR);
      }
    }
    
    /**
     * Destructor
     *
     * @access  public
     */
    function __destruct() {
      $this->message->__destruct();
      parent::__destruct();
    }
  }
?>
