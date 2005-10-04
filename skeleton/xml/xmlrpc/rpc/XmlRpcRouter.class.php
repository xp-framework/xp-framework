<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'xml.xmlrpc.rpc.XmlRpcRequest',
    'xml.xmlrpc.rpc.XmlRpcResponse',
    'scriptlet.HttpScriptlet'
  );

  /**
   * XML-RPC Router class. You can use this class to implement
   * a XML-RPC webservice.
   *
   * <code>
   *   require('lang.base.php');
   *   xp::sapi('xmlrpc.service');
   * 
   *   $s= &new XmlRpcRouter(new ClassLoader('net.xp_framework.webservices.xmlrpc'));
   * 
   *   try(); {
   *     $s->init();
   *     $response= &$s->process();
   *   } if (catch('HttpScriptletException', $e)) {
   * 
   *     // Retrieve standard "Internal Server Error"-Document
   *     $response= &$e->getResponse();
   *   }
   * 
   *   $response->sendHeaders();
   *   $response->sendContent();
   * 
   *   $s->finalize();
   * </code>
   *
   * The default implementation of the XmlRpcRouter takes the given methodName from the
   * XML-RPC request, splits it at the '.' and takes the first part as the class name,
   * the second part as the method name. A request on a server with the setup given above
   * and a requested methodName of 'XmlRpcTest.runTest' would try to instanciate class
   * net.xp_framework.webservices.xmlrpc.XmlRpcTestHandler and run methon 'runTest'
   * on it.
   *
   * @ext      xml
   * @see      xp://xml.xmlrpc.XmlRpcClient
   * @purpose  XML-RPC-Service
   */
  class XmlRpcRouter extends HttpScriptlet {
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
     * Create a request object.
     *
     * @access  protected
     * @return  &xml.xmlrpc.rpc.XmlRpcRequest
     */
    function &_request() {
      return new XmlRpcRequest();
    }

    /**
     * Create a response object.
     *
     * @access  protected
     * @return  &xml.xmlrpc.rpc.XmlRpcResponse
     */
    function &_response() {
      return new XmlRpcResponse();
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
     * Handle GET requests. XML-RPC requests are only sent via HTTP POST,
     * so GET isn't supported.
     *
     * @access  public
     * @param   &xml.xmlrpc.rpc.XmlRpcRequest request
     * @param   &xml.xmlrpc.rpc.XmlRpcResponse response
     */
    function doGet(&$request, &$response) {
      return throw(new IllegalAccessException('GET is not supported'));
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
      try(); {

        // Get message
        $msg= &$request->getMessage();

        // Figure out encoding if given
        $type= $request->getHeader('Content-type');
        if (FALSE !== ($pos= strpos($type, 'charset='))) {
          $msg->setEncoding(substr($type, $pos+ 8));
        }

        // Create answer
        $answer= &new XmlRpcMessage();
        $answer->create(XMLRPC_RESPONSE);

        // Call handler
        $return= &$this->callReflectHandler($msg);
        $answer->setData($return);

      } if (catch('Exception', $e)) {
      
        // An exception occured
        $answer->setFault(HTTP_INTERNAL_SERVER_ERROR, $e->toString());
      }
      
      // Set message
      $response->setHeader('Content-type', 'text/xml; charset='.$answer->encoding);
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
    
      // Determine requested class and method
      if (FALSE === strpos($msg->method, '.'))
        return throw(new IllegalArgumentException('Malformed method: "'.$msg->method.'"'));
        
      list($className, $methodName)= explode('.', $msg->method, 2);
    
      // Create message from request data
      try(); {
        $class= &$this->classloader->loadClass(ucfirst($className).'Handler');
      } if (catch('ClassNotFoundException', $e)) {
        return throw($e);
      }

      // Check if method can be handled
      if (!$class->hasMethod($methodName)) {
        return throw(new IllegalArgumentException(
          $class->getName().' cannot handle method '.$methodName
        ));
      }

      with ($method= &$class->getMethod($methodName)); {

        // Check if this method is a webmethod
        if (!$method->hasAnnotation('webmethod')) {
          return throw(new IllegalAccessException('Cannot access non-web method '.$msg->method));
        }

        // Create instance and invoke method
        return $method->invoke($class->newInstance(), $msg->getData());
      }
    }
  }
?>
