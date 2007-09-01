<?php
/* This class is part of the XP framework
 *
 * $Id: AjpConnection.class.php 8974 2006-12-27 17:29:09Z friebe $ 
 */

  namespace org::apache::ajp;

  ::uses('peer.http.HttpConnection', 'org.apache.ajp.AjpRequest');

  /**
   * AJP connection
   *
   * <code>
   *   uses('org.apache.ajp.AjpConnection');
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
   *     'SERVER_SIGNATURE'  => 'org.apache.ajp.AjpConnection',
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
  class AjpConnection extends peer::http::HttpConnection {
  
    /**
     * Constructor
     *
     * @param   mixed url a string or a peer.URL object
     * @param   array env environment
     */
    public function __construct($url, $env) {
      parent::__construct($url);
      $this->request->setEnvironment($env);
    }
  
    /**
     * Create the request object
     *
     * @param   &mixed url a string or a peer.URL object
     */
    protected function _createRequest($url) {
      $this->request= new AjpRequest($url);
    }  
  }
?>
