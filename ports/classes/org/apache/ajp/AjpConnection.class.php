<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('peer.http.HttpConnection', 'peer.ajp.AjpRequest');

  /**
   * AJP connection
   *
   * <code>
   *   uses('peer.ajp.AjpConnection');
   *   
   *   $ajp= &new AjpConnection('ajpv12://localhost:8007/xml/static?', array(
   *     'SERVER_NAME'       => 'example.com',
   *     'DOCUMENT_ROOT'     => '/home/httpd/',
   *     'PATH_INFO'         => '/',
   *     'PATH_TRANSLATED'   => '/xml/static',
   *     'REMOTE_ADDR'       => '127.0.0.1',
   *     'REMOTE_HOST'       => 'localhost',
   *     'REMOTE_USER'       => NULL,
   *     'SCRIPT_NAME'       => 'static',
   *     'SERVER_PORT'       => 80,
   *     'SERVER_PROTOCOL'   => 'HTTP',
   *     'SERVER_SIGNATURE'  => 'peer.ajp.AjpConnection',
   *     'SERVER_SOFTWARE'   => 'PHP'
   *   ));
   *   try(); {
   *     $r= &$ajp->get();
   *   } if (catch('Exception', $e)) {
   *     $e->printStackTrace();
   *     exit();
   *   }
   *
   *   var_dump($r->getHeaders());
   * </code>
   *
   * @see      http://jakarta.apache.org/builds/jakarta-tomcat-connectors/jk/release/v1.2.1/src/
   * @see      http://jakarta.apache.org/tomcat/tomcat-3.2-doc/AJPv13.html
   * @purpose  Java servlet integration
   */
  class AjpConnection extends HttpConnection {
  
    /**
     * Constructor
     *
     * @access  public
     * @param   mixed url a string or a peer.URL object
     * @param   array env environment
     */
    function __construct($url, $env) {
      parent::__construct($url);
      $this->request->setEnvironment($env);
    }
  
    /**
     * Create the request object
     *
     * @access  protected
     * @param   &mixed url a string or a peer.URL object
     */
    function _createRequest(&$url) {
      $this->request= &new AjpRequest($url);
    }  
  }
?>
