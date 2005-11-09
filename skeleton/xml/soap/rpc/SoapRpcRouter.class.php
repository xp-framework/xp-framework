<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
  uses(
    'scriptlet.HttpScriptlet',
    'xml.soap.rpc.SoapRpcRequest',
    'xml.soap.rpc.SoapRpcResponse',
    'xml.soap.SOAPMessage',
    'xml.soap.SOAPMapping'
  );

  /**
   * Serves as a working base for SOAP request passed to a CGI
   * executed in an Apache environment.
   *
   * Example:
   * <code>
   *   uses('xml.soap.rpc.SoapRpcRouter');
   *
   *   $s= &new SoapRpcRouter(new ClassLoader('info.binford6100.webservices'));
   *   try(); {
   *     $s->init();
   *     $response= &$s->process();
   *   } if (catch('HttpScriptletException', $e)) {
   *     // Retrieve standard "Internal Server Error"-Document
   *     $response= &$e->getResponse();
   *   }
   *   $response->sendHeaders();
   *   $response->sendContent();
   *
   *   $s->finalize();
   * </code>
   *
   * Pass the classpath to the handlers to the constructor of this class
   * to where your handlers are. Handlers are the classes that do the
   * work for the requested SOAP-Action.
   *
   * Example: Let's say, the SOAP-Action passed in is Ident#echoStruct, and
   * the constructor was given the classpath info.binford6100.webservices,
   * the rpc router would look for a class with the fully qualified name
   * info.binford6100.webservices.IdentHandler and call it's method echoStruct.
   *
   * @see scriptlet.HttpScriptlet
   */
  class SoapRpcRouter extends HttpScriptlet {
    var
      $classloader = NULL,
      $mapping     = NULL;

    /**
     * Constructor
     *
     * @access  public
     * @param   &lang.ClassLoader classloader
     */
    function __construct(&$classloader) {
      $this->classloader= &$classloader;
      $this->mapping= &new SOAPMapping();
    }
    
    /**
     * Create a request object.
     *
     * @access  protected
     * @return  &xml.soap.rpc.SoapRpcRequest
     */
    function &_request() {
      return new SoapRpcRequest();
    }

    /**
     * Create a response object. Override this method to define
     * your own response object
     *
     * @access  protected
     * @return  &xml.soap.rpc.SoapRpcResponse
     */
    function &_response() {
      return new SoapRpcResponse();
    }

    /**
     * Handle GET requests. Since SOAP over HTTP is defined via
     * HTTP POST, throw an exception. We could also provide a usage
     * example, but this may be going too far.
     *
     * @access  public
     * @param   &xml.soap.rpc.SoapRpcRequest request
     * @param   &xml.soap.rpc.SoapRpcResponse response
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
     * Handle POST requests. The complete POST data consits of the SOAP
     * XML message.
     *
     * @access  public
     * @param   &xml.soap.rpc.SoapRpcRequest request
     * @param   &xml.soap.rpc.SoapRpcResponse response
     */
    function doPost(&$request, &$response) {
      try(); {

        // Get message
        $msg= &$request->getMessage();

        // Figure out encoding if given
        $type= $request->getHeader('Content-type');
        if (FALSE !== ($pos= strpos($type, 'charset='))) {
          $msg->setEncoding(substr($type, $pos+ 8));
        }

        // Create answer
        $answer= &new SOAPMessage();
        $answer->create($msg->action, $msg->method.'Response');

        // Call handler
        $return= &$this->callReflectHandler($msg);
        $answer->setData(array($return));

      } if (catch('ServiceException', $e)) {
      
        // Server methods may throw a ServerFaultException to have more
        // conveniant control over the faultcode which is returned to the client.
        $answer->setFault(
          $e->getFaultcode(),
          $e->getMessage(),
          $request->getEnvValue('SERVER_NAME').':'.$request->getEnvValue('SERVER_PORT'),
          $this->formatStackTrace($e->getStackTrace())
        );

      } if (catch('Exception', $e)) {
        $answer->setFault(
          HTTP_INTERNAL_SERVER_ERROR,
          $e->message,
          $request->getEnvValue('SERVER_NAME').':'.$request->getEnvValue('SERVER_PORT'),
          $this->formatStackTrace($e->getStackTrace())
        );
      }

      // Set message
      $response->setHeader('Content-type', 'text/xml; charset='.$answer->encoding);
      $response->setMessage($answer);
    }

    /**
     * Calls the handler that the action reflects to
     *
     * @access  protected
     * @param   &xml.soap.SOAPMessage message object (from request)
     * @return  &mixed result of method call
     * @throws  lang.IllegalArgumentException if there is no such method
     * @throws  lang.IllegalAccessException for non-public methods
     */
    function &callReflectHandler(&$msg) {

      // Create message from request data
      try(); {
        $class= &$this->classloader->loadClass($msg->action.'Handler');
      } if (catch('ClassNotFoundException', $e)) {
        return throw($e);
      }

      // Check if method can be handled
      if (!$class->hasMethod($msg->method)) {
        return throw(new IllegalArgumentException(
          $class->getName().' cannot handle method '.$msg->method
        ));
      }

      with ($method= &$class->getMethod($msg->method)); {
      
        // Check if this method is a webmethod
        if (!$method->hasAnnotation('webmethod')) {
          return throw(new IllegalAccessException('Cannot access non-web method '.$msg->method));
        }

        // Create instance and invoke method
        return $method->invoke($class->newInstance(), $msg->getData(NULL, $this->mapping));
      }
    }
  }
?>
