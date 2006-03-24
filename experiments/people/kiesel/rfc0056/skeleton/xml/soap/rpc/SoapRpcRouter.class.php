<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
  uses(
    'scriptlet.rpc.AbstractRpcRouter',
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
  class SoapRpcRouter extends AbstractRpcRouter {
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
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function _message() {
      return new SOAPMessage();
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
  }
?>
