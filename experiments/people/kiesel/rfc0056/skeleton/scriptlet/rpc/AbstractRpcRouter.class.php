<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'scriptlet.HttpScriptlet',
    'util.ServiceException',
    'scriptlet.rpc.RpcFault'
  );

  /**
   * Abstract RPC router
   *
   * @purpose  Provide RPC services
   */
  class AbstractRpcRouter extends HttpScriptlet {
    var
      $classloader  = NULL,
      $cat          = NULL;
    
    /**
     * Constructor
     *
     * @access  public
     * @param   &lang.ClassLoader classloader
     */
    function __construct(&$classloader) {
      $this->classloader= &$classloader;
    }
    
    /**
     * Set trace for debugging
     *
     * @access  public
     * @param   &util.log.LogCategory cat
     */
    function setTrace(&$cat) {
      $this->cat= &$cat;
    }
    
    /**
     * Create a request object.
     *
     * @model   abstract
     * @access  protected
     * @return  &scriptlet.rpc.AbstractRpcRequest
     */
    function &_request() {}

    /**
     * Create a response object.
     *
     * @model   abstract
     * @access  protected
     * @return  &scriptlet.rpc.AbstractRpcResponse
     */
    function &_response() {}

    /**
     * Create a message object.
     *
     * @model   abstract
     * @access  protected
     * @return  &scriptlet.rpc.AbstractRpcMessage
     */
    function &_message() {}

    /**
     * Handle GET requests. XML-RPC requests are only sent via HTTP POST,
     * so GET isn't supported.
     *
     * @access  public
     * @param   &scriptlet.rpc.AbstractRpcRequest request
     * @param   &scriptlet.rpc.AbstractRpcResponse response
     */
    function doGet(&$request, &$response) {
      return throw(new IllegalAccessException('GET is not supported'));
    }

    /**
     * Formats stack trace to be used in SOAP fault messages. Makes sure
     * the stack trace elements' string representation is XML-safe (unsafe 
     * characters are replaced with the character ¿ (ASCII #191)).
     *
     * @access  protected
     * @param   lang.StackTraceElement[] elements
     * @return  string[]
     */
    function formatStackTrace($elements) {
      $stacktrace= array();
      $replace= str_repeat('¿', strlen(XML_ILLEGAL_CHARS));
      for ($i= 0, $s= sizeof($elements); $i < $s; $i++) {
        $stacktrace[]= strtr($elements[$i]->toString(), XML_ILLEGAL_CHARS, $replace); 
      }
      return $stacktrace;
    }

    /**
     * Handle POST requests. The POST data carries the XML-RPC
     * request.
     *
     * @access  public
     * @param   &xml.xmlrpc.rpc.XmlRpcRequest request
     * @param   &xml.xmlrpc.rpc.XmlRpcResponse response
     */
    function doPost(&$request, &$response) {
      $this->cat && $response->setTrace($this->cat);
      $this->cat && $request->setTrace($this->cat);
      
      $hasFault= FALSE;
      try(); {

        // Get message
        $msg= &$request->getMessage();
        $msg->setEncoding($request->getEncoding());

        // Create answer
        $answer= &$this->_message();
        $answer->create($msg);

        // Call handler
        $return= &$this->callReflectHandler($msg);

      } if (catch('ServiceException', $e)) {
      
        // Server methods may throw a ServerFaultException to have more
        // conveniant control over the faultcode which is returned to the client.
        $answer->setFault(
          $e->getFaultcode(),
          $e->getMessage(),
          $request->getEnvValue('SERVER_NAME').':'.$request->getEnvValue('SERVER_PORT'),
          $this->formatStackTrace($e->getStackTrace())
        );
        $hasFault= TRUE;
        
      } if (catch('Exception', $e)) {
      
        $answer->setFault(
          HTTP_INTERNAL_SERVER_ERROR,
          $e->message,
          $request->getEnvValue('SERVER_NAME').':'.$request->getEnvValue('SERVER_PORT'),
          $this->formatStackTrace($e->getStackTrace())
        );
        $hasFault= TRUE;
      }
      
      $hasFault || $answer->setData($return);
      
      // Set message
      $response->setHeader('Content-type', $answer->getContentType().'; charset='.$answer->getEncoding());
      $response->setMessage($answer);
    }
    
    /**
     * Calls the handler that the action reflects to
     *
     * @access  protected
     * @param   &xml.xmlrpc.XmlRpcMessage message object (from request)
     * @return  &mixed result of method call
     * @throws  lang.IllegalArgumentException if there is no such method
     * @throws  lang.IllegalAccessException for non-public methods
     */
    function &callReflectHandler(&$msg) {
    
      // Check on valid params
      if (0 == strlen($msg->getMethod())) {
        return throw(new IllegalArgumentException('No method name passed.'));
      }

      // Create message from request data
      try(); {
        $class= &$this->classloader->loadClass(ucfirst($msg->getClass()).'Handler');
      } if (catch('ClassNotFoundException', $e)) {
        return throw($e);
      }

      // Check if method can be handled
      if (!$class->hasMethod($msg->getMethod())) {
        return throw(new IllegalArgumentException(
          $class->getName().' cannot handle method '.$msg->getMethod()
        ));
      }

      with ($method= &$class->getMethod($msg->getMethod())); {

        // Check if this method is a webmethod
        if (!$method->hasAnnotation('webmethod')) {
          return throw(new IllegalAccessException('Cannot access non-web method '.$msg->getMethod()));
        }

        // Create instance and invoke method
        return $method->invoke($class->newInstance(), $msg->getData());
      }
    }
  } implements(__FILE__, 'util.log.Traceable');
?>
