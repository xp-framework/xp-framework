<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
  uses(
    'org.apache.HttpScriptlet',
    'xml.soap.rpc.SoapRpcRequest',
    'xml.soap.rpc.SoapRpcResponse',
    'xml.soap.SOAPMessage'
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
   * @see org.apache.HttpScriptlet
   */
  class SoapRpcRouter extends HttpScriptlet {
    var
      $classloader= NULL;

    /**
     * Constructor
     *
     * @access  public
     * @param   string handlerClassPath the class path in its notation xxx.yyy.zzz
     *          to the location where the classes are located
     */
    function __construct(&$classloader) {
      $this->classloader= &$classloader;
      parent::__construct();
    }

    /**
     * Set our own response object
     *
     * @see     org.apache.HttpScriptlet#_response
     */
    function _response() {
      $this->response= &new SoapRpcResponse();
    }

    /**
     * Set our own request object
     *
     * @see     org.apache.HttpScriptlet#_request
     */
    function _request() {
      $this->request= &new SoapRpcRequest();
    }

    /**
     * Handle GET requests. Since SOAP over HTTP is defined via
     * HTTP POST, throw an exception. We could also provide a usage
     * example, but this may be going to far.
     *
     * @see     org.apache.HttpScriptlet#doGet
     */
    function doGet(&$request, &$response) {
      return throw(new IllegalAccessException('GET is not supported'));
    }

    /**
     * Handle POST requests. The complete POST data consits
     *
     * @see     org.apache.HttpScriptlet#doGet
     */
    function doPost(&$request, &$response) {
      try(); {

        // Get message
        $msg= &$request->getMessage();

        // Figure out encoding if given
        $type= $request->getHeader('Content-type');
        if (FALSE !== ($pos= strpos($type, 'charset='))) {
          $msg->encoding= substr($type, $pos+ 8);
        }

        // Create answer
        $answer= &new SOAPMessage();
        $answer->create($msg->action, $msg->method.'Response');

        // Call handler
        $return= &$this->callReflectHandler($msg);
        $answer->setData(array($return));

      } if (catch('Exception', $e)) {
        foreach ($e->getStackTrace() as $element) {
          $stacktrace[]= $element->toString();
        }
        
        // An exception occured
        $answer->setFault(
          HTTP_INTERNAL_SERVER_ERROR,
          $e->message,
          $request->getEnvValue('SERVER_NAME').':'.$request->getEnvValue('SERVER_PORT'),
          $stacktrace
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
      if ('_' == $msg->method{0}) {
        return throw(new IllegalAccessException('Cannot access non-public method '.$msg->method));
      }

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

      // Create instance and invoke method
      with ($method= &$class->getMethod($msg->method)); {
        return $method->invoke(
          $class->newInstance(),
          $msg->getData(NULL)
        );
      }
    }

  }
?>
