<?php
/* This class is part of the XP framework
 *
 * $Id: HttpRequestFactory.class.php 10594 2007-06-11 10:04:54Z friebe $ 
 */

  namespace peer::http;

  ::uses(
    'peer.URL',
    'peer.http.HttpRequest',
    'peer.http.HttpsRequest'
  );

  /**
   * Request factory. Used internally by the HttpConnection class.
   *
   * @see      xp://peer.http.HttpConnection
   * @purpose  Factory for HTTP / HTTPS
   */
  class HttpRequestFactory extends lang::Object {
  
    /**
     * Factory method
     *
     * @param   peer.URL an url object
     * @return  lang.Object a request object
     * @throws  lang.IllegalArgumentException in case the scheme is not supported
     */
    public static function factory($url) {
      switch ($url->getScheme()) {
        case 'http':
          return new HttpRequest($url);
          
        case 'https':
          return new HttpsRequest($url);
        
        default:
          throw(new lang::IllegalArgumentException('Scheme "'.$url->getScheme().'" not supported'));
      }
    }
  }
?>
