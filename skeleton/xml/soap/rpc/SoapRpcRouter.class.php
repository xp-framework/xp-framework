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
   *
   */
  class SoapRpcRouter extends HttpScriptlet {
    var 
      $handlerClassPath = '';
    
    /**
     * Constructor
     *
     * @access  public
     * @param   string handlerClassPath the class path in its notation xxx.yyy.zzz
     *          to the location where the classes are located
     */
    function __construct($handlerClassPath) {
      $this->handlerClassPath= $handlerClassPath;
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
      
        // Create answer
        $answer= &new SOAPMessage();
        $answer->create($msg->action, $msg->method.'Response');
      
        // Call handler
        $return= &$this->callReflectHandler($msg);
        $answer->setData(array($return));
        
      } if (catch('Exception', $e)) {
      
        // An exception occured
        $answer->setFault(
          HTTP_INTERNAL_SERVER_ERROR,
          $e->message,
          $request->getEnvValue('REQUEST_URI'),
          $e->getStackTrace()
        );
      }
      
      // Set message
      $response->setMessage($answer);
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function &callReflectHandler(&$msg) {

      // Create message from request data
      try(); {
        $reflect= ClassLoader::loadClass($this->handlerClassPath.'.'.$msg->action.'Handler');
        
        // Check if method can be handled
        if (!in_array(strtolower($msg->method), get_class_methods($reflect))) return throw(new IllegalArgumentException(
          $reflect.' cannot handle method '.$msg->method
        ));
      } if (catch('Exception', $e)) {
        return throw($e);
      }
      
      // Create instance
      $handler= &new $reflect();
      
      // Call method
      $return= call_user_func_array(
        array(&$handler, $msg->method),
        $msg->getData(NULL)
      );
     
      // Clean up
      $handler->__destruct();
      
      // Return data
      return $return;
    }

  }
?>
