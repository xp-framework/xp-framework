<?php
/* Diese Klasse ist Teil des XP-Frameworks
 *
 * $Id$
 */

  uses(
    'org.apache.HTTPModuleRequest',
    'org.apache.HTTPModuleResponse',
    'org.apache.HTTPModuleException'
  );

  // HTTP-Methoden
  define('HTTP_METHOD_GET',     'GET');
  define('HTTP_METHOD_POST',    'POST');
  
  /**
   * Kapselt eine Apache-PHP-Modul-Anwendung
   *
   * Beispiel-Anwendung in <main>:
   * <pre>
   * $mod= new HTTPModule();
   * try(); {
   *   $response= $mod->process();
   * } catch('HTTPModuleException', $e) {
   *   $response= $e->getResponse(); // Standard HTTP/1.1 500 Error-Doc
   * }
   * 
   * $response->sendHeaders();
   * echo $response->getContent();
   * 
   * $mod->finalize();
   * </pre>
   */
  class HTTPModule extends Object {
    var
      $request,
      $response; 
    
    var 
      $_method= NULL;
  
    /**
     * Constructor
     *
     * @access  
     * @param   
     * @return  
     */  
    function __construct() {
      $this->request= &new HTTPModuleRequest();
      $this->response= &new HTTPModuleResponse();
      parent::__construct();
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   string method Request-Methode
     */
    function _handleMethod($method) {
      switch ($method) {
        case HTTP_METHOD_POST:
          $this->request->data= &$GLOBALS['HTTP_RAW_POST_DATA'];
          $this->request->params= &$_POST;
          $this->_method= 'doPost';
          break;
          
        case HTTP_METHOD_GET:
          $this->request->data= getenv('QUERY_STRING');
          $this->request->params= &$_GET;
          $this->_method= 'doGet';
          break;
      }
      
      $this->request->method= $method;
    }
    
    /**
     * Wird für GET aufgerufen
     *
     * @access  private
     */
    function doGet() {
    }
    
    /**
     * Wird für POST aufgerufen
     *
     * @access  private
     */
    function doPost() {
    }
    
    function init() {
    
    }
    
    /**
     * Wird am "Seitenende" aufgerufen, nachdem bereits Header und Body 
     * abgeschickt wurden
     *
     * @access  public
     */
    function finalize() {
    }

    /**
     * Wird von außen augerufen und arbeitet die Requests ab
     *
     * @access  public
     * @return  bool Success
     * @throws  org.apache.HTTPModuleException Eine Exception
     */
    function &process() {
      $this->_handleMethod(getenv('REQUEST_METHOD'));
      $return= call_user_func_array(array(&$this, $this->_method), array(
        &$this->request,
        &$this->response
      ));
      return $this->response;
    }
  }
?>
