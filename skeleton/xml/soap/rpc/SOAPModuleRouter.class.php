<?php
/* Diese Klasse ist Teil des XP-Frameworks
 *
 * $Id$
 */
  uses(
    'org.apache.HTTPModule',
    'xml.soap.SOAPMessage'
  );
  
  class SOAPModuleRouter extends HTTPModule {
    var
      $handler;
      
    function __construct($handler) {
      parent::__construct();
      $this->handler= $handler;
    }
    
    function callReflectHandler($msg) {

      // Create message from request data
      try(); {
        $reflect= ClassLoader::loadClass($this->handler.'.'.$msg->action.'Handler');
        
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
    
    /**
     * Handle post requests
     *
     * @access  
     * @param   
     * @return  
     */
    function doPost($request, &$response) {
    
      // Get request
      $log= &Logger::getInstance();
      $log->warn($request);
      
      $msg= &new SOAPMessage();
      list(
        $msg->action, 
        $msg->method
      )= explode('#', str_replace('"', '', $request->getHeader('SOAPAction')));
      
      // Create answer
      $answer= &new SOAPMessage();
      $answer->create($msg->action, $msg->method.'Response');
      
      try(); {
        $msg->fromString($request->getData()) && 
        $answer->setData(array(
          $this->callReflectHandler(&$msg)
        ));
      } if (catch('Exception', $e)) {
        $answer->setFault(
          'Server',
          $e->message,
          'http://'.getenv('SERVER_NAME').':'.getenv('SERVER_PORT').'/',
          $e->getStackTrace()
        );
        $response->statusCode= 500;
      }
      
      $response->addHeader('Server', 'SOAP#');
      $response->content= (
        $answer->getDeclaration().
        "\n".
        $answer->getSource(0)
      );
      
      // Indicate we handled this request
      return TRUE;
    }
    
  }
?>
