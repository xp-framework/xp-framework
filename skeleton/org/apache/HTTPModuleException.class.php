<?php
/* Diese Klasse ist Teil des XP-Frameworks
 *
 * $Id$
 */

  /**
   * Kapselt die HTTPModuleException
   *
   * @see org.apache.HTTPModule
   */  
  class HTTPModuleException extends Object {
    var
      $response;
      
    function __construct($message) {
      $this->_createResponse();
      parent::__construct($message);
    }
    
    function &getResponse() {
      return $this->response;
    }
    
    function _createResponse() {
      $this->response= &new HTTPModuleResponse(array(
        'statusCode'    => 500,
        'content'       => 'Internal Server Error'
      ));
    }
  }
?>
