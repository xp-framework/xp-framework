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
   * $mod= &new HTTPModule();
   * try(); {
   *   $mod->init();
   *   $response= &$mod->process();
   * } catch('HTTPModuleException', $e) {
   *   $response= &$e->getResponse(); // Standard HTTP/1.1 500 Error-Doc
   * }
   * 
   * $response->sendHeaders();
   * $response->sendContent();
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
      $this->_createRequest();
      $this->_createResponse();
      parent::__construct();
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function _createRequest() {
      $this->request= &new HTTPModuleRequest();
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function _createResponse() {
      $this->response= &new HTTPModuleResponse();
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   string method Request-Methode
     */
    function _handleMethod($method) {
      $this->request->headers= getallheaders();
      $this->request->method= $method;
      
      switch ($method) {
        case HTTP_METHOD_POST:
          $this->request->setData($GLOBALS['HTTP_RAW_POST_DATA']);
          $this->request->setParams($_POST);
          $this->_method= 'doPost';
          break;
          
        case HTTP_METHOD_GET:
          $this->request->setData(getenv('QUERY_STRING'));
          $this->request->setParams($_GET);
          $this->_method= 'doGet';
          break;
          
        // PHP doesn't support more methods, anyway
        default:
          throw(new MethodNotImplementedException($method.' not supported'));
      }
    }
    
    /**
     * Wird für GET aufgerufen
     *
     * @access  private
     */
    function doGet(&$req, &$res) {
    }
    
    /**
     * Wird für POST aufgerufen
     *
     * @access  private
     */
    function doPost(&$req, &$res) {
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
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
      if (FALSE === (call_user_func_array(array(&$this, $this->_method), array(
        &$this->request,
        &$this->response
      )))) {
        return FALSE;
      }
      return $this->response;
    }
  }
?>
