<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('peer.http.HttpConnection');
  
  define('WEBDAV_METHOD_PROPFIND',  'PROPFIND');
  define('WEBDAV_METHOD_PROPPATCH', 'PROPPATCH');
  define('WEBDAV_METHOD_MKCOL',     'MKCOL');
  define('WEBDAV_METHOD_LOCK',      'LOCK');
  define('WEBDAV_METHOD_UNLOCK',    'UNLOCK');
  define('WEBDAV_METHOD_COPY',      'COPY');
  define('WEBDAV_METHOD_MOVE',      'MOVE');
  define('WEBDAV_METHOD_DELETE',    'DELETE');
  define('WEBDAV_METHOD_REPORT',    'REPORT');
  define('WEBDAV_METHOD_VERSION',   'VERSION-CONTROL');
  
  /**
   * Webdav connection
   *   
   * @purpose  Provide a webdav connection
   */
  class WebdavConnection extends HttpConnection {
  

    /**
     * Perform a Propfind request
     *
     * @access  public
     * @param   mixed arg default NULL
     * @param   array headers default array()
     * @return  &peer.http.HttpResponse response object
     */
    public function propfind($arg= NULL, $headers= array()) {
      return $this->request(WEBDAV_METHOD_PROPFIND, $arg, $headers);
    } 
    
    /**
     * Perform a Proppatch request
     *
     * @access  public
     * @param   mixed arg default NULL
     * @param   array headers default array()
     * @return  &peer.http.HttpResponse response object
     */
    public function proppatch($arg= NULL, $headers= array()) {
      return $this->request(WEBDAV_METHOD_PROPPATCH, new RequestData($arg), $headers);
    } 
    
    /**
     * Perform a mkCol request
     *
     * @access  public
     * @param   mixed arg default NULL
     * @param   array headers default array()
     * @return  &peer.http.HttpResponse response object
     */
    public function mkcol($arg= NULL, $headers= array()) {
      return $this->request(WEBDAV_METHOD_MKCOL, $arg, $headers);
    }  
  
    /**
     * Perform a lock request
     *
     * @access  public
     * @param   mixed arg default NULL
     * @param   array headers default array()
     * @return  &peer.http.HttpResponse response object
     */
    public function lock($arg= NULL, $headers= array()) {
      return $this->request(WEBDAV_METHOD_LOCK, $arg, $headers);
    } 
    
    /**
     * Perform a unlock request
     *
     * @access  public
     * @param   mixed arg default NULL
     * @param   array headers default array()
     * @return  &peer.http.HttpResponse response object
     */
    public function unlock($arg= NULL, $headers= array()) {
      return $this->request(WEBDAV_METHOD_UNLOCK, $arg, $headers);
    }
    
    /**
     * Perform a copy request
     *
     * @access  public
     * @param   mixed arg default NULL
     * @param   array headers default array()
     * @return  &peer.http.HttpResponse response object
     */
    public function copy($arg= NULL, $headers= array()) {
      return $this->request(WEBDAV_METHOD_COPY, $arg, $headers);
    }   
    
    /**
     * Perform a move request
     *
     * @access  public
     * @param   mixed arg default NULL
     * @param   array headers default array()
     * @return  &peer.http.HttpResponse response object
     */
    public function move($arg= NULL, $headers= array()) {
      return $this->request(WEBDAV_METHOD_MOVE, $arg, $headers);
    }
    
    /**
     * Perform a delete request
     *
     * @access  public
     * @param   mixed arg default NULL
     * @param   array headers default array()
     * @return  &peer.http.HttpResponse response object
     */
    public function delete($arg= NULL, $headers= array()) {
      return $this->request(WEBDAV_METHOD_DELETE, $arg, $headers);
    }
    
    /**
     * Perform a VersionControl request
     *
     * @access  public
     * @param   mixed arg default NULL
     * @param   array headers default array()
     * @return  &peer.http.HttpResponse response object
     */
    public function version($arg= NULL, $headers= array()) {
      return $this->request(WEBDAV_METHOD_VERSION, $arg, $headers);
    }
    
    /**
     * Perform a report request
     *
     * @access  public
     * @param   mixed arg default NULL
     * @param   array headers default array()
     * @return  &peer.http.HttpResponse response object
     */
    public function report($arg= NULL, $headers= array()) {
      return $this->request(WEBDAV_METHOD_REPORT, $arg, $headers);
    }
  }
?>
