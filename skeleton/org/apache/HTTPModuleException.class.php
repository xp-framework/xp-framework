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
      $this->response= new HTTPModuleResponse(array(
        'statusCode'    => 500,
        'content'       => 'Internal Server Error'
      ));
      parent::__construct($message);
    }
  }
?>
