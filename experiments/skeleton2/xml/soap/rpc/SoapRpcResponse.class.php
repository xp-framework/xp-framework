<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('org.apache.HttpScriptletResponse');

  /**
   * Wraps SOAP response
   *
   * @see org.apache.HttpScriptletResponse  
   */
  class SoapRpcResponse extends HttpScriptletResponse {
    public 
      $message= NULL;
    
    /**
     * Constructor
     *
     * @access  public
     */
    public function __construct() {
      self::setHeader('Server', 'SOAP 1.0#/PHP'.phpversion().'/'.php_uname());
      self::setHeader('Content-type', 'text/xml');
      parent::__construct();
    }
    
    /**
     * Overwritten method from parent class
     *
     * @access  public
     * @throws  IllegalAccessException
     * @see     #setMessage
     */
    public function write() {
      throw (IllegalAccessException('Cannot write directly'));
    }
    
    /**
     * Overwritten method from parent class
     *
     * @access  public
     * @throws  IllegalAccessException
     * @see     #setMessage
     */
    public function setContent() {
      throw (IllegalAccessException('Cannot write directly'));
    }
    
    /**
     * Sets SOAP message
     *
     * @access  public
     * @param   &xml.soap.SOAPMessage msg SOAPmessage object
     */
    public function setMessage(&$msg) {
      $this->message= $msg;
    }
    
    /**
     * Gets content for HTTP response
     *
     * @access  public
     * @return  string content
     * @see     org.apache.HttpScriptletResponse#getContent
     */
    public function getContent() {
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
    public function process() {
      if (NULL !== $this->message->getFault()) {
        self::setStatus(HTTP_INTERNAL_SERVER_ERROR);
      }
    }
    
    /**
     * Destructor
     *
     * @access  public
     */
    public function __destruct() {
      if (is_a($this->message, 'Object')) $this->message->__destruct();
      parent::__destruct();
    }
  }
?>
